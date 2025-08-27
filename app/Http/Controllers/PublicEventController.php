<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PublicEventController extends Controller
{
    /**
     * Display public events for a hotel
     */
    public function index($hotelSlug = null)
    {
        $query = Event::query()
            ->with('hotel')
            ->where('is_public', true)
            ->where('is_active', true)
            ->where('status', 'published')
            ->where('start_date', '>', Carbon::now())
            ->orderBy('start_date', 'asc');

        if ($hotelSlug) {
            $query->whereHas('hotel', function ($q) use ($hotelSlug) {
                $q->where('slug', $hotelSlug);
            });
        }

        $events = $query->paginate(12);

        return view('public.events.index', compact('events', 'hotelSlug'));
    }

    /**
     * Display a specific public event
     */
    public function show($hotelSlug, Event $event)
    {
        // Verify the event belongs to the hotel and is public
        if (!$event->is_public || !$event->is_active || $event->status !== 'published') {
            abort(404);
        }

        if ($event->hotel->slug !== $hotelSlug) {
            abort(404);
        }

        $registrations = $event->confirmedRegistrations()->count();
        $availableSpots = $event->getAvailableSpots();

        return view('public.events.show', compact('event', 'registrations', 'availableSpots'));
    }

    /**
     * Show registration form for an event
     */
    public function register($hotelSlug, Event $event)
    {
        // Verify the event belongs to the hotel and is public
        if (!$event->is_public || !$event->is_active || $event->status !== 'published') {
            abort(404);
        }

        if ($event->hotel->slug !== $hotelSlug) {
            abort(404);
        }

        // Check if registration is closed
        if ($event->isRegistrationClosed()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'Registration for this event is closed.');
        }

        // Check if event is full
        if ($event->isFull()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'This event is at full capacity.');
        }

        // Check if event has passed
        if ($event->start_date->isPast()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'This event has already passed.');
        }

        return view('public.events.register', compact('event'));
    }

    /**
     * Process event registration
     */
    public function processRegistration(Request $request, $hotelSlug, Event $event)
    {
        // Debug output - Step 1: Method entry
        if (request()->get('debug') === 'step1') {
            return response()->json([
                'step' => '1 - Method entry',
                'hotelSlug' => $hotelSlug,
                'eventId' => $event->id,
                'eventTitle' => $event->title,
                'requestData' => $request->all(),
                'message' => '✅ Method is being called successfully!'
            ]);
        }

        // Verify the event belongs to the hotel and is public
        if (!$event->is_public || !$event->is_active || $event->status !== 'published') {
            abort(404);
        }

        if ($event->hotel->slug !== $hotelSlug) {
            abort(404);
        }

        // Check if registration is closed
        if ($event->isRegistrationClosed()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'Registration for this event is closed.');
        }

        // Check if event is full
        if ($event->isFull()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'This event is at full capacity.');
        }

        // Check if event has passed
        if ($event->start_date->isPast()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('error', 'This event has already passed.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'number_of_guests' => 'required|integer|min:1|max:10',
            'special_requests' => 'nullable|string|max:1000',
            'guest_details' => 'nullable|array',
            'guest_details.*.name' => 'nullable|string|max:255',
            'guest_details.*.email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Check capacity
        $availableSpots = $event->getAvailableSpots();
        if ($availableSpots !== -1 && $data['number_of_guests'] > $availableSpots) {
            return redirect()->back()
                ->with('error', "Only {$availableSpots} spots available for this event.")
                ->withInput();
        }

        // Check if email is already registered for this event
        $existingRegistration = $event->registrations()
            ->where('email', $data['email'])
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existingRegistration) {
            return redirect()->back()
                ->with('error', 'You are already registered for this event.')
                ->withInput();
        }

        // Calculate total amount
        $totalAmount = $event->price * $data['number_of_guests'];

        // Create registration
        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'number_of_guests' => $data['number_of_guests'],
            'total_amount' => $totalAmount,
            'status' => 'confirmed', // Auto-confirm the registration
            'special_requests' => $data['special_requests'],
            'guest_details' => $data['guest_details'] ?? null
        ]);

        // Try to find and link member if exists
        $member = Member::where('hotel_id', $event->hotel_id)
            ->where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if ($member) {
            $registration->update(['member_id' => $member->id]);
        }

        // Ensure registration code is generated and registration is confirmed
        if (empty($registration->registration_code)) {
            $registration->update(['registration_code' => EventRegistration::generateRegistrationCode()]);
        }
        
        if ($registration->status === 'confirmed' && empty($registration->confirmed_at)) {
            $registration->update(['confirmed_at' => now()]);
        }

        // Debug output - Final step: Registration created successfully
        if (request()->get('debug') === '1') {
            return response()->json([
                'step' => 'Final - Registration created successfully',
                'message' => '✅ Registration created and ready to redirect to confirmation',
                'registration' => [
                    'id' => $registration->id,
                    'name' => $registration->name,
                    'email' => $registration->email,
                    'status' => $registration->status,
                    'registration_code' => $registration->registration_code
                ],
                'redirect_url' => route('public.events.confirmation', [$hotelSlug, $event, $registration->id])
            ]);
        }

        // Show success page directly instead of redirecting
        return view('public.events.success', [
            'event' => $event,
            'registration' => $registration,
            'hotel' => $event->hotel
        ])->with('success', 'Registration submitted successfully!');
    }

    /**
     * Show registration confirmation
     */
    public function confirmation($hotelSlug, $eventId, $registrationId)
    {
        // Debug output for confirmation method
        if (request()->get('debug') === 'step1') {
            return response()->json([
                'step' => 'Confirmation method called',
                'hotelSlug' => $hotelSlug,
                'eventId' => $eventId,
                'registrationId' => $registrationId,
                'url' => request()->url(),
                'message' => '✅ Confirmation method is being called'
            ]);
        }

        // Debug logging for method entry
        \Log::info('Public confirmation method entry', [
            'hotelSlug' => $hotelSlug,
            'eventId' => $eventId,
            'registrationId' => $registrationId,
            'url' => request()->url()
        ]);

        // Manually resolve the event to ensure it exists
        $event = Event::find($eventId);
        
        if (!$event) {
            \Log::error('Event not found', ['eventId' => $eventId]);
            abort(404, 'Event not found');
        }

        // Debug output - Event found
        if (request()->get('debug') === 'step2') {
            return response()->json([
                'step' => 'Event found successfully',
                'eventId' => $eventId,
                'eventTitle' => $event->title,
                'message' => '✅ Event exists'
            ]);
        }

        // Manually resolve the registration to ensure it exists
        $registration = EventRegistration::find($registrationId);
        
        if (!$registration) {
            \Log::error('Registration not found', ['registrationId' => $registrationId]);
            abort(404, 'Registration not found');
        }

        // Debug output - Registration found
        if (request()->get('debug') === 'step3') {
            return response()->json([
                'step' => 'Registration found successfully',
                'registrationId' => $registrationId,
                'registrationName' => $registration->name,
                'registrationEventId' => $registration->event_id,
                'message' => '✅ Registration exists'
            ]);
        }

        // Debug logging
        \Log::info('Public confirmation method called', [
            'hotelSlug' => $hotelSlug,
            'eventId' => $event->id,
            'registrationId' => $registration->id,
            'registrationEventId' => $registration->event_id,
            'eventHotelSlug' => $event->hotel->slug,
            'registrationCode' => $registration->registration_code,
            'registrationStatus' => $registration->status,
            'registrationExists' => $registration->exists,
            'url' => request()->url()
        ]);

        // Verify the registration belongs to the event
        if ($registration->event_id !== $event->id) {
            if (request()->get('debug') === 'step4') {
                return response()->json([
                    'step' => 'Registration event mismatch',
                    'registrationEventId' => $registration->event_id,
                    'eventId' => $event->id,
                    'message' => '❌ Registration does not belong to this event'
                ]);
            }
            \Log::error('Registration event mismatch', [
                'registrationEventId' => $registration->event_id,
                'eventId' => $event->id
            ]);
            abort(404);
        }

        // Verify the event belongs to the hotel
        if ($event->hotel->slug !== $hotelSlug) {
            if (request()->get('debug') === 'step5') {
                return response()->json([
                    'step' => 'Event hotel mismatch',
                    'eventHotelSlug' => $event->hotel->slug,
                    'hotelSlug' => $hotelSlug,
                    'message' => '❌ Event does not belong to this hotel'
                ]);
            }
            \Log::error('Event hotel mismatch', [
                'eventHotelSlug' => $event->hotel->slug,
                'hotelSlug' => $hotelSlug
            ]);
            abort(404);
        }

        return view('public.events.confirmation', compact('event', 'registration'));
    }

    /**
     * Cancel a registration
     */
    public function cancelRegistration($hotelSlug, Event $event, $registrationId)
    {
        // Manually resolve the registration to ensure it exists
        $registration = EventRegistration::find($registrationId);
        
        if (!$registration) {
            abort(404, 'Registration not found');
        }

        // Verify the registration belongs to the event
        if ($registration->event_id !== $event->id) {
            abort(404);
        }

        // Verify the event belongs to the hotel
        if ($event->hotel->slug !== $hotelSlug) {
            abort(404);
        }

        // Check if registration can be cancelled
        if (!in_array($registration->status, ['pending', 'confirmed'])) {
            return redirect()->back()
                ->with('error', 'This registration cannot be cancelled.');
        }

        // Check if event has started
        if ($event->start_date->isPast()) {
            return redirect()->back()
                ->with('error', 'Cannot cancel registration for an event that has already started.');
        }

        if ($registration->cancel()) {
            return redirect()->route('public.events.show', [$hotelSlug, $event])
                ->with('success', 'Registration cancelled successfully.');
        }

        return redirect()->back()
            ->with('error', 'Unable to cancel registration.');
    }

    /**
     * Search for registration by code
     */
    public function searchRegistration(Request $request, $hotelSlug)
    {
        $validator = Validator::make($request->all(), [
            'registration_code' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $registration = EventRegistration::where('registration_code', $request->registration_code)
            ->with(['event.hotel'])
            ->first();

        if (!$registration) {
            return redirect()->back()
                ->with('error', 'Registration not found.');
        }

        // Verify the event belongs to the hotel
        if ($registration->event->hotel->slug !== $hotelSlug) {
            return redirect()->back()
                ->with('error', 'Registration not found.');
        }

        return view('public.events.registration-details', compact('registration'));
    }

    /**
     * Show registration search form
     */
    public function searchForm($hotelSlug)
    {
        return view('public.events.search', compact('hotelSlug'));
    }
}
