<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Hotel;
use App\Services\MemberCardGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MemberCardController extends Controller
{
    protected $cardGenerator;

    public function __construct(MemberCardGenerator $cardGenerator)
    {
        $this->cardGenerator = $cardGenerator;
    }

    /**
     * Show the virtual cards management page
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);
        
        // Get hotels for filter (super admin can see all, others see only their hotel)
        $hotels = $user->role === 'super_admin' 
            ? Hotel::orderBy('name')->get()
            : collect([$user->hotel]);

        // Get members with card statistics
        $query = Member::with(['hotel', 'membershipType'])
            ->when($hotelId, function($q) use ($hotelId) {
                $q->where('hotel_id', $hotelId);
            })
            ->when($user->role !== 'super_admin', function($q) use ($user) {
                $q->where('hotel_id', $user->hotel_id);
            });

        $totalMembers = $query->count();
        $membersWithCards = $query->whereNotNull('card_image_path')->count();
        $membersWithoutCards = $totalMembers - $membersWithCards;

        // Get paginated members for display
        $members = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('members.cards.index', compact(
            'members', 
            'hotels', 
            'hotelId', 
            'totalMembers', 
            'membersWithCards', 
            'membersWithoutCards'
        ));
    }

    /**
     * Generate card for a single member
     */
    public function generateCard(Member $member)
    {
        try {
            $user = auth()->user();
            
            // Check permissions
            if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
                return back()->with('error', 'You do not have permission to generate cards for this member.');
            }

            // Generate the card
            $cardPath = $this->cardGenerator->generate($member);
            
            // Update member with card path
            $member->update(['card_image_path' => $cardPath]);

            // Generate QR code if it doesn't exist
            if (!$member->hasQRCode()) {
                try {
                    $qrService = app(\App\Services\QRCodeService::class);
                    $qrPath = $qrService->generateForMember($member);
                    
                    Log::info('QR code generated successfully with card', [
                        'member_id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'qr_path' => $qrPath,
                        'generated_by' => $user->id
                    ]);
                } catch (\Exception $qrError) {
                    Log::warning('Failed to generate QR code with card', [
                        'member_id' => $member->id,
                        'error' => $qrError->getMessage()
                    ]);
                    // Don't fail the card generation if QR fails
                }
            }

            Log::info('Member card generated successfully', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'card_path' => $cardPath,
                'generated_by' => $user->id
            ]);

            return back()->with('success', "Card generated successfully for {$member->full_name}");

        } catch (\Exception $e) {
            Log::error('Failed to generate member card', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to generate card: ' . $e->getMessage());
        }
    }

    /**
     * Mass generate cards for members without cards
     */
    public function massGenerate(Request $request)
    {
        $request->validate([
            'hotel_id' => 'nullable|exists:hotels,id',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:members,id'
        ]);

        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);

        try {
            DB::beginTransaction();

            // Build query for members without cards
            $query = Member::whereNull('card_image_path')
                ->when($hotelId, function($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                })
                ->when($user->role !== 'super_admin', function($q) use ($user) {
                    $q->where('hotel_id', $user->hotel_id);
                })
                ->when($request->has('member_ids'), function($q) use ($request) {
                    $q->whereIn('id', $request->member_ids);
                });

            $membersToProcess = $query->get();
            
            if ($membersToProcess->isEmpty()) {
                return back()->with('info', 'No members found that need cards generated.');
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($membersToProcess as $member) {
                try {
                    // Generate the card
                    $cardPath = $this->cardGenerator->generate($member);
                    
                    // Update member with card path
                    $member->update(['card_image_path' => $cardPath]);

                    // Generate QR code if it doesn't exist
                    if (!$member->hasQRCode()) {
                        try {
                            $qrService = app(\App\Services\QRCodeService::class);
                            $qrService->generateForMember($member);
                        } catch (\Exception $qrError) {
                            // Don't fail the card generation if QR fails
                            Log::warning('Failed to generate QR code in mass operation', [
                                'member_id' => $member->id,
                                'error' => $qrError->getMessage()
                            ]);
                        }
                    }
                    
                    $successCount++;

                    Log::info('Member card generated in mass operation', [
                        'member_id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'card_path' => $cardPath,
                        'generated_by' => $user->id
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Member {$member->full_name} ({$member->membership_id}): " . $e->getMessage();
                    
                    Log::error('Failed to generate member card in mass operation', [
                        'member_id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $message = "Successfully generated {$successCount} cards.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} cards failed to generate.";
            }

            return back()->with([
                'success' => $message,
                'card_generation_errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Mass card generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Mass card generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a member's card
     */
    public function downloadCard(Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to download this card.');
        }

        if (!$member->card_image_path) {
            return back()->with('error', 'No card found for this member.');
        }

        $filePath = storage_path('app/public/' . $member->card_image_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Card file not found.');
        }

        return response()->download($filePath, "card_{$member->membership_id}.jpg");
    }

    /**
     * View a member's card
     */
    public function viewCard(Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to view this card.');
        }

        if (!$member->card_image_path) {
            return back()->with('error', 'No card found for this member.');
        }

        $cardUrl = Storage::url($member->card_image_path);
        
        return view('members.cards.view', compact('member', 'cardUrl'));
    }

    /**
     * Delete a member's card
     */
    public function deleteCard(Member $member)
    {
        $user = auth()->user();
        
        // Check permissions
        if ($user->role !== 'super_admin' && $user->hotel_id !== $member->hotel_id) {
            return back()->with('error', 'You do not have permission to delete this card.');
        }

        if ($member->card_image_path) {
            // Delete the file
            Storage::disk('public')->delete($member->card_image_path);
            
            // Update member record
            $member->update(['card_image_path' => null]);

            Log::info('Member card deleted', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'deleted_by' => $user->id
            ]);

            return back()->with('success', "Card deleted for {$member->full_name}");
        }

        return back()->with('info', 'No card found for this member.');
    }

    /**
     * Get card statistics for AJAX
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $hotelId = $request->input('hotel_id', $user->hotel_id);

        $query = Member::when($hotelId, function($q) use ($hotelId) {
            $q->where('hotel_id', $hotelId);
        })
        ->when($user->role !== 'super_admin', function($q) use ($user) {
            $q->where('hotel_id', $user->hotel_id);
        });

        $totalMembers = $query->count();
        $membersWithCards = $query->whereNotNull('card_image_path')->count();
        $membersWithoutCards = $totalMembers - $membersWithCards;

        return response()->json([
            'total' => $totalMembers,
            'with_cards' => $membersWithCards,
            'without_cards' => $membersWithoutCards,
            'percentage' => $totalMembers > 0 ? round(($membersWithCards / $totalMembers) * 100, 1) : 0
        ]);
    }
    
    /**
     * Debug card template configuration for a member
     */
    public function debugCard(Member $member)
    {
        // Check if user has permission to view this member
        if (auth()->user()->hotel_id !== $member->hotel_id) {
            abort(403);
        }
        
        $generator = new \App\Services\MemberCardGenerator();
        $debugInfo = $generator->debugCardTemplate($member);
        
        return response()->json($debugInfo);
    }
}
