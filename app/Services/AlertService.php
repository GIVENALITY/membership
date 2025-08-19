<?php

namespace App\Services;

use App\Models\MemberAlert;
use App\Models\MemberAlertTrigger;
use App\Models\Member;
use Illuminate\Support\Facades\Log;

class AlertService
{
    /**
     * Check all active alerts for a member
     */
    public function checkMemberAlerts(Member $member): void
    {
        $alerts = MemberAlert::where('hotel_id', $member->hotel_id)
            ->where('is_active', true)
            ->get();

        foreach ($alerts as $alert) {
            $this->checkAlert($alert, $member);
        }
    }

    /**
     * Check a specific alert for a member
     */
    public function checkAlert(MemberAlert $alert, Member $member): void
    {
        // Check if member already has an active trigger for this alert
        $existingTrigger = MemberAlertTrigger::where('member_alert_id', $alert->id)
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->first();

        if ($existingTrigger) {
            return; // Already triggered
        }

        // Check if the member triggers this alert
        if ($alert->checkMember($member)) {
            $this->createTrigger($alert, $member);
        }
    }

    /**
     * Create a trigger for an alert
     */
    public function createTrigger(MemberAlert $alert, Member $member): void
    {
        try {
            $triggerData = $this->buildTriggerData($alert, $member);

            MemberAlertTrigger::create([
                'member_alert_id' => $alert->id,
                'member_id' => $member->id,
                'hotel_id' => $member->hotel_id,
                'trigger_data' => $triggerData,
                'status' => 'active',
                'triggered_at' => now(),
            ]);

            Log::info("Alert triggered: {$alert->name} for member {$member->full_name}");

            // TODO: Send email notification if enabled
            if ($alert->send_email) {
                $this->sendEmailNotification($alert, $member);
            }

        } catch (\Exception $e) {
            Log::error("Failed to create alert trigger: " . $e->getMessage(), [
                'alert_id' => $alert->id,
                'member_id' => $member->id,
            ]);
        }
    }

    /**
     * Build trigger data based on alert type
     */
    private function buildTriggerData(MemberAlert $alert, Member $member): array
    {
        $data = [
            'alert_name' => $alert->name,
            'alert_type' => $alert->type,
            'member_name' => $member->full_name,
            'member_id' => $member->membership_id,
            'triggered_at' => now()->toISOString(),
        ];

        switch ($alert->type) {
            case 'spending_threshold':
                $data['current_spending'] = $this->getCurrentSpending($member, $alert->conditions['period'] ?? 'month');
                $data['threshold'] = $alert->conditions['amount'] ?? 0;
                break;

            case 'visit_frequency':
                $lastVisit = $this->getLastVisit($member);
                $data['last_visit'] = $lastVisit ? $lastVisit->created_at->toISOString() : null;
                $data['days_since_last_visit'] = $lastVisit ? now()->diffInDays($lastVisit->created_at) : null;
                $data['threshold_days'] = $alert->conditions['days'] ?? 30;
                break;

            case 'points_threshold':
                $data['current_points'] = $member->current_points_balance;
                $data['threshold_points'] = $alert->conditions['points'] ?? 0;
                $data['operator'] = $alert->conditions['operator'] ?? 'gte';
                break;

            case 'birthday_approaching':
                if ($member->birth_date) {
                    $birthday = \Carbon\Carbon::parse($member->birth_date);
                    $nextBirthday = $birthday->copy()->setYear(now()->year);
                    
                    if ($nextBirthday->isPast()) {
                        $nextBirthday->addYear();
                    }
                    
                    $data['next_birthday'] = $nextBirthday->toISOString();
                    $data['days_until_birthday'] = now()->diffInDays($nextBirthday, false);
                }
                break;
        }

        return $data;
    }

    /**
     * Get current spending for a member in a given period
     */
    private function getCurrentSpending(Member $member, string $period): float
    {
        $query = \App\Models\DiningVisit::where('member_id', $member->id)
            ->where('is_checked_out', true)
            ->where('hotel_id', $member->hotel_id);

        switch ($period) {
            case 'day':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        return $query->sum('final_amount');
    }

    /**
     * Get the last visit for a member
     */
    private function getLastVisit(Member $member): ?\App\Models\DiningVisit
    {
        return \App\Models\DiningVisit::where('member_id', $member->id)
            ->where('is_checked_out', true)
            ->where('hotel_id', $member->hotel_id)
            ->latest('created_at')
            ->first();
    }

    /**
     * Send email notification for an alert
     */
    private function sendEmailNotification(MemberAlert $alert, Member $member): void
    {
        // TODO: Implement email sending logic
        // This would use Laravel's mail system to send notifications
        Log::info("Email notification would be sent for alert: {$alert->name}");
    }

    /**
     * Check all members for all active alerts
     */
    public function checkAllMembers(): void
    {
        $hotels = \App\Models\Hotel::all();

        foreach ($hotels as $hotel) {
            $members = Member::where('hotel_id', $hotel->id)
                ->where('status', 'active')
                ->get();

            foreach ($members as $member) {
                $this->checkMemberAlerts($member);
            }
        }
    }

    /**
     * Resolve triggers that no longer apply
     */
    public function resolveExpiredTriggers(): void
    {
        $activeTriggers = MemberAlertTrigger::where('status', 'active')
            ->with(['alert', 'member'])
            ->get();

        foreach ($activeTriggers as $trigger) {
            if (!$trigger->alert->checkMember($trigger->member)) {
                $trigger->resolve('Automatically resolved - conditions no longer met');
            }
        }
    }
}
