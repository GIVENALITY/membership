<?php

namespace App\Http\Controllers;

use App\Models\MemberAlert;
use App\Models\MemberAlertTrigger;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberAlertController extends Controller
{
    /**
     * Display a listing of alerts
     */
    public function index()
    {
        $user = Auth::user();
        $alerts = MemberAlert::where('hotel_id', $user->hotel_id)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTriggers = MemberAlertTrigger::where('hotel_id', $user->hotel_id)
            ->where('status', 'active')
            ->with(['alert', 'member'])
            ->orderBy('triggered_at', 'desc')
            ->get();

        return view('alerts.index', compact('alerts', 'activeTriggers'));
    }

    /**
     * Show the form for creating a new alert
     */
    public function create()
    {
        $alertTypes = [
            'spending_threshold' => 'Spending Threshold',
            'visit_frequency' => 'Visit Frequency',
            'points_threshold' => 'Points Threshold',
            'birthday_approaching' => 'Birthday Approaching',
            'membership_expiry' => 'Membership Expiry',
            'custom' => 'Custom Alert',
        ];

        $severityLevels = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];

        return view('alerts.create', compact('alertTypes', 'severityLevels'));
    }

    /**
     * Store a newly created alert
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:spending_threshold,visit_frequency,points_threshold,birthday_approaching,membership_expiry,custom',
            'severity' => 'required|in:low,medium,high,critical',
            'is_active' => 'boolean',
            'send_email' => 'boolean',
            'show_dashboard' => 'boolean',
            'show_quickview' => 'boolean',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'email_template' => 'nullable|string',
        ]);

        $conditions = $this->buildConditions($request);
        
        $alert = MemberAlert::create([
            'hotel_id' => $user->hotel_id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'conditions' => $conditions,
            'severity' => $request->severity,
            'is_active' => $request->boolean('is_active', true),
            'send_email' => $request->boolean('send_email', false),
            'show_dashboard' => $request->boolean('show_dashboard', true),
            'show_quickview' => $request->boolean('show_quickview', true),
            'color' => $request->color ?? '#ffc107',
            'email_template' => $request->email_template,
        ]);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully!');
    }

    /**
     * Show the form for editing an alert
     */
    public function edit(MemberAlert $alert)
    {
        $this->authorizeAlert($alert);

        $alertTypes = [
            'spending_threshold' => 'Spending Threshold',
            'visit_frequency' => 'Visit Frequency',
            'points_threshold' => 'Points Threshold',
            'birthday_approaching' => 'Birthday Approaching',
            'membership_expiry' => 'Membership Expiry',
            'custom' => 'Custom Alert',
        ];

        $severityLevels = [
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
        ];

        return view('alerts.edit', compact('alert', 'alertTypes', 'severityLevels'));
    }

    /**
     * Update the specified alert
     */
    public function update(Request $request, MemberAlert $alert)
    {
        $this->authorizeAlert($alert);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:spending_threshold,visit_frequency,points_threshold,birthday_approaching,membership_expiry,custom',
            'severity' => 'required|in:low,medium,high,critical',
            'is_active' => 'boolean',
            'send_email' => 'boolean',
            'show_dashboard' => 'boolean',
            'show_quickview' => 'boolean',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
            'email_template' => 'nullable|string',
        ]);

        $conditions = $this->buildConditions($request);
        
        $alert->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'conditions' => $conditions,
            'severity' => $request->severity,
            'is_active' => $request->boolean('is_active', true),
            'send_email' => $request->boolean('send_email', false),
            'show_dashboard' => $request->boolean('show_dashboard', true),
            'show_quickview' => $request->boolean('show_quickview', true),
            'color' => $request->color ?? '#ffc107',
            'email_template' => $request->email_template,
        ]);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert updated successfully!');
    }

    /**
     * Remove the specified alert
     */
    public function destroy(MemberAlert $alert)
    {
        $this->authorizeAlert($alert);
        
        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully!');
    }

    /**
     * Show alert triggers
     */
    public function triggers(MemberAlert $alert)
    {
        $this->authorizeAlert($alert);

        $triggers = $alert->triggers()
            ->with(['member'])
            ->orderBy('triggered_at', 'desc')
            ->paginate(20);

        return view('alerts.triggers', compact('alert', 'triggers'));
    }

    /**
     * Acknowledge an alert trigger
     */
    public function acknowledge(MemberAlertTrigger $trigger, Request $request)
    {
        $this->authorizeTrigger($trigger);
        
        $trigger->acknowledge(Auth::user(), $request->notes);

        return back()->with('success', 'Alert acknowledged successfully!');
    }

    /**
     * Resolve an alert trigger
     */
    public function resolve(MemberAlertTrigger $trigger, Request $request)
    {
        $this->authorizeTrigger($trigger);
        
        $trigger->resolve($request->notes);

        return back()->with('success', 'Alert resolved successfully!');
    }

    /**
     * Test an alert against all members
     */
    public function test(MemberAlert $alert)
    {
        $this->authorizeAlert($alert);

        $members = Member::where('hotel_id', $alert->hotel_id)
            ->where('status', 'active')
            ->get();

        $triggeredMembers = [];
        
        foreach ($members as $member) {
            if ($alert->checkMember($member)) {
                $triggeredMembers[] = $member;
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($triggeredMembers) . ' members would trigger this alert',
            'members' => $triggeredMembers->take(10)->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->full_name,
                    'membership_id' => $member->membership_id,
                    'email' => $member->email,
                ];
            }),
        ]);
    }

    /**
     * Get active alerts for dashboard/quickview
     */
    public function getActiveAlerts()
    {
        $user = Auth::user();
        
        $alerts = MemberAlert::where('hotel_id', $user->hotel_id)
            ->where('is_active', true)
            ->get();

        $activeTriggers = MemberAlertTrigger::where('hotel_id', $user->hotel_id)
            ->where('status', 'active')
            ->with(['alert', 'member'])
            ->orderBy('triggered_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'alerts' => $alerts,
            'activeTriggers' => $activeTriggers,
        ]);
    }

    /**
     * Build conditions array based on alert type
     */
    private function buildConditions(Request $request): array
    {
        $type = $request->type;
        $conditions = [];

        switch ($type) {
            case 'spending_threshold':
                $conditions = [
                    'amount' => $request->input('spending_amount', 0),
                    'period' => $request->input('spending_period', 'month'),
                ];
                break;

            case 'visit_frequency':
                $conditions = [
                    'days' => $request->input('visit_days', 30),
                ];
                break;

            case 'points_threshold':
                $conditions = [
                    'points' => $request->input('points_amount', 0),
                    'operator' => $request->input('points_operator', 'gte'),
                ];
                break;

            case 'birthday_approaching':
                $conditions = [
                    'days_ahead' => $request->input('birthday_days', 7),
                ];
                break;

            case 'membership_expiry':
                $conditions = [
                    'days_ahead' => $request->input('expiry_days', 30),
                ];
                break;

            case 'custom':
                $conditions = [
                    'custom_condition' => $request->input('custom_condition', ''),
                ];
                break;
        }

        return $conditions;
    }

    /**
     * Authorize alert access
     */
    private function authorizeAlert(MemberAlert $alert): void
    {
        if ($alert->hotel_id !== Auth::user()->hotel_id) {
            abort(403, 'Unauthorized access to this alert.');
        }
    }

    /**
     * Authorize trigger access
     */
    private function authorizeTrigger(MemberAlertTrigger $trigger): void
    {
        if ($trigger->hotel_id !== Auth::user()->hotel_id) {
            abort(403, 'Unauthorized access to this alert trigger.');
        }
    }
}
