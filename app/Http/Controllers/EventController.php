<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index()
    {
        $user = Auth::user();
        $events = Event::where('hotel_id', $user->hotel_id)
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'max_capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['hotel_id'] = $user->hotel_id;
        $data['status'] = 'draft';

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified event
     */
    public function show(Event $event)
    {
        $this->authorizeEvent($event);
        
        $registrations = $event->registrations()
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_registrations' => $event->registrations()->count(),
            'confirmed_registrations' => $event->confirmedRegistrations()->count(),
            'pending_registrations' => $event->pendingRegistrations()->count(),
            'total_guests' => $event->confirmedRegistrations()->sum('number_of_guests'),
            'available_spots' => $event->getAvailableSpots(),
            'is_full' => $event->isFull()
        ];

        return view('events.show', compact('event', 'registrations', 'stats'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit(Event $event)
    {
        $this->authorizeEvent($event);
        
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'max_capacity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:draft,published,cancelled,completed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }

        $event->update($data);

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event
     */
    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        
        // Delete image if exists
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Publish an event
     */
    public function publish(Event $event)
    {
        $this->authorizeEvent($event);
        
        $event->update(['status' => 'published']);

        return redirect()->back()
            ->with('success', 'Event published successfully.');
    }

    /**
     * Cancel an event
     */
    public function cancel(Event $event)
    {
        $this->authorizeEvent($event);
        
        $event->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'Event cancelled successfully.');
    }

    /**
     * Show registrations for an event
     */
    public function registrations(Event $event)
    {
        $this->authorizeEvent($event);
        
        $registrations = $event->registrations()
            ->with('member')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('events.registrations', compact('event', 'registrations'));
    }

    /**
     * Confirm a registration
     */
    public function confirmRegistration(EventRegistration $registration)
    {
        $this->authorizeEvent($registration->event);
        
        if ($registration->confirm()) {
            return redirect()->back()
                ->with('success', 'Registration confirmed successfully.');
        }

        return redirect()->back()
            ->with('error', 'Unable to confirm registration.');
    }

    /**
     * Cancel a registration
     */
    public function cancelRegistration(EventRegistration $registration)
    {
        $this->authorizeEvent($registration->event);
        
        if ($registration->cancel()) {
            return redirect()->back()
                ->with('success', 'Registration cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', 'Unable to cancel registration.');
    }

    /**
     * Mark registration as attended
     */
    public function markAttended(EventRegistration $registration)
    {
        $this->authorizeEvent($registration->event);
        
        if ($registration->markAsAttended()) {
            return redirect()->back()
                ->with('success', 'Registration marked as attended.');
        }

        return redirect()->back()
            ->with('error', 'Unable to mark registration as attended.');
    }

    /**
     * Search members for event registration
     */
    public function searchMembers(Request $request, Event $event)
    {
        $this->authorizeEvent($event);
        
        $query = $request->get('query');
        
        $members = Member::where('hotel_id', Auth::user()->hotel_id)
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();

        return response()->json($members);
    }

    /**
     * Register a member for an event
     */
    public function registerMember(Request $request, Event $event)
    {
        $this->authorizeEvent($event);
        
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'number_of_guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $member = Member::findOrFail($request->member_id);
        
        // Check if member is already registered
        $existingRegistration = $event->registrations()
            ->where('member_id', $member->id)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existingRegistration) {
            return redirect()->back()
                ->with('error', 'Member is already registered for this event.');
        }

        // Check capacity
        if ($event->isFull()) {
            return redirect()->back()
                ->with('error', 'Event is at full capacity.');
        }

        $totalAmount = $event->price * $request->number_of_guests;

        EventRegistration::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'name' => $member->name,
            'email' => $member->email,
            'phone' => $member->phone,
            'number_of_guests' => $request->number_of_guests,
            'total_amount' => $totalAmount,
            'status' => 'confirmed',
            'special_requests' => $request->special_requests,
            'confirmed_at' => Carbon::now()
        ]);

        return redirect()->back()
            ->with('success', 'Member registered successfully for the event.');
    }

    /**
     * Export event registrations
     */
    public function exportRegistrations(Event $event)
    {
        $this->authorizeEvent($event);
        
        $registrations = $event->registrations()
            ->with('member')
            ->get();

        $filename = 'event_registrations_' . $event->id . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($registrations) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Registration Code',
                'Name',
                'Email',
                'Phone',
                'Number of Guests',
                'Total Amount',
                'Status',
                'Special Requests',
                'Registered At',
                'Member ID'
            ]);

            // Add data
            foreach ($registrations as $registration) {
                fputcsv($file, [
                    $registration->registration_code,
                    $registration->name,
                    $registration->email,
                    $registration->phone,
                    $registration->number_of_guests,
                    $registration->total_amount,
                    $registration->status,
                    $registration->special_requests,
                    $registration->registered_at->format('Y-m-d H:i:s'),
                    $registration->member_id
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Authorize event access
     */
    private function authorizeEvent(Event $event)
    {
        $user = Auth::user();
        
        if ($event->hotel_id !== $user->hotel_id) {
            abort(403, 'Unauthorized access to event.');
        }
    }
}
