<?php

namespace App\Http\Controllers;

use App\Models\PointsConfiguration;
use App\Models\PointsMultiplier;
use App\Models\PointsTier;
use App\Services\PointsCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointsConfigurationController extends Controller
{
    protected $pointsService;

    public function __construct(PointsCalculationService $pointsService)
    {
        $this->pointsService = $pointsService;
    }

    /**
     * Display the points configuration dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $hotelId = $user->hotel_id;

        $configurations = PointsConfiguration::where('hotel_id', $hotelId)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $multipliers = PointsMultiplier::where('hotel_id', $hotelId)
            ->orderBy('created_at', 'desc')
            ->get();

        $tiers = PointsTier::where('hotel_id', $hotelId)
            ->orderBy('min_points')
            ->get();

        $summary = $this->pointsService->getAvailableConfigurations($hotelId);

        return view('points-configuration.index', compact(
            'configurations',
            'multipliers',
            'tiers',
            'summary'
        ));
    }

    /**
     * Show the form for creating a new points configuration
     */
    public function create()
    {
        $configurationTypes = [
            'dining_visit' => 'Dining Visit',
            'special_event' => 'Special Event',
            'referral' => 'Referral',
            'social_media' => 'Social Media',
            'birthday_bonus' => 'Birthday Bonus',
            'holiday_bonus' => 'Holiday Bonus',
            'custom' => 'Custom',
        ];

        return view('points-configuration.create', compact('configurationTypes'));
    }

    /**
     * Store a newly created points configuration
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:dining_visit,special_event,referral,social_media,birthday_bonus,holiday_bonus,custom',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $rules = $this->buildRules($request);
        
        $configuration = PointsConfiguration::create([
            'hotel_id' => $user->hotel_id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'rules' => $rules,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('points-configuration.index')
            ->with('success', 'Points configuration created successfully!');
    }

    /**
     * Show the form for editing a points configuration
     */
    public function edit(PointsConfiguration $configuration)
    {
        $this->authorizeConfiguration($configuration);

        $configurationTypes = [
            'dining_visit' => 'Dining Visit',
            'special_event' => 'Special Event',
            'referral' => 'Referral',
            'social_media' => 'Social Media',
            'birthday_bonus' => 'Birthday Bonus',
            'holiday_bonus' => 'Holiday Bonus',
            'custom' => 'Custom',
        ];

        return view('points-configuration.edit', compact('configuration', 'configurationTypes'));
    }

    /**
     * Update the specified points configuration
     */
    public function update(Request $request, PointsConfiguration $configuration)
    {
        $this->authorizeConfiguration($configuration);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:dining_visit,special_event,referral,social_media,birthday_bonus,holiday_bonus,custom',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $rules = $this->buildRules($request);
        
        $configuration->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'rules' => $rules,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->input('sort_order', 0),
        ]);

        return redirect()->route('points-configuration.index')
            ->with('success', 'Points configuration updated successfully!');
    }

    /**
     * Remove the specified points configuration
     */
    public function destroy(PointsConfiguration $configuration)
    {
        $this->authorizeConfiguration($configuration);
        
        $configuration->delete();

        return redirect()->route('points-configuration.index')
            ->with('success', 'Points configuration deleted successfully!');
    }

    /**
     * Test points calculation
     */
    public function test(Request $request)
    {
        $user = Auth::user();
        $sampleData = $request->all();
        
        $result = $this->pointsService->testCalculation($user->hotel_id, $sampleData);

        return response()->json($result);
    }

    /**
     * Show multiplier management
     */
    public function multipliers()
    {
        $user = Auth::user();
        $multipliers = PointsMultiplier::where('hotel_id', $user->hotel_id)
            ->with('membershipType')
            ->orderBy('created_at', 'desc')
            ->get();

        $membershipTypes = \App\Models\MembershipType::where('hotel_id', $user->hotel_id)
            ->where('is_active', true)
            ->get();

        return view('points-configuration.multipliers', compact('multipliers', 'membershipTypes'));
    }

    /**
     * Show tiers management
     */
    public function tiers()
    {
        $user = Auth::user();
        $tiers = PointsTier::where('hotel_id', $user->hotel_id)
            ->orderBy('min_points')
            ->get();

        return view('points-configuration.tiers', compact('tiers'));
    }

    /**
     * Build rules array based on configuration type
     */
    private function buildRules(Request $request): array
    {
        $type = $request->type;
        $rules = [];

        switch ($type) {
            case 'dining_visit':
                $rules = [
                    'points_per_person' => $request->input('points_per_person', 1),
                    'points_per_amount' => $request->input('points_per_amount', 0),
                    'points_per_person_spending' => $request->input('points_per_person_spending', 0),
                    'min_spending_per_person' => $request->input('min_spending_per_person', 0),
                    'max_people' => $request->input('max_people', 0),
                ];
                break;

            case 'special_event':
                $rules = [
                    'base_points' => $request->input('base_points', 0),
                ];
                break;

            case 'referral':
                $rules = [
                    'base_points' => $request->input('base_points', 0),
                    'points_by_type' => [
                        'new_member' => $request->input('points_new_member', 0),
                        'returning_member' => $request->input('points_returning_member', 0),
                    ],
                ];
                break;

            case 'social_media':
                $rules = [
                    'base_points' => $request->input('base_points', 0),
                    'points_by_platform' => [
                        'facebook' => $request->input('points_facebook', 0),
                        'instagram' => $request->input('points_instagram', 0),
                        'twitter' => $request->input('points_twitter', 0),
                        'general' => $request->input('points_general', 0),
                    ],
                ];
                break;

            case 'birthday_bonus':
                $rules = [
                    'bonus_points' => $request->input('bonus_points', 0),
                ];
                break;

            case 'holiday_bonus':
                $rules = [
                    'base_points' => $request->input('base_points', 0),
                    'points_by_holiday' => [
                        'christmas' => $request->input('points_christmas', 0),
                        'new_year' => $request->input('points_new_year', 0),
                        'valentine' => $request->input('points_valentine', 0),
                        'general' => $request->input('points_general', 0),
                    ],
                ];
                break;

            case 'custom':
                $rules = [
                    'base_points' => $request->input('base_points', 0),
                ];
                break;
        }

        return $rules;
    }

    /**
     * Authorize configuration access
     */
    private function authorizeConfiguration(PointsConfiguration $configuration): void
    {
        if ($configuration->hotel_id !== Auth::user()->hotel_id) {
            abort(403, 'Unauthorized access to this configuration.');
        }
    }
}
