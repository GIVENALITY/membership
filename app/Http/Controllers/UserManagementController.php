<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users for the hotel.
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->canManageUsers()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $users = User::where('hotel_id', $user->hotel_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user-management.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = auth()->user();
        
        if (!$user->canManageUsers()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return view('user-management.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canManageUsers()) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:manager,cashier,frontdesk'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create the new user
        $newUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'hotel_id' => $user->hotel_id,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', "User '{$newUser->name}' created successfully with role: {$newUser->role_display_name}");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers() || $user->hotel_id !== $currentUser->hotel_id) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        return view('user-management.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers() || $user->hotel_id !== $currentUser->hotel_id) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:manager,cashier,frontdesk'],
            'phone' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update user
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $passwordValidator = Validator::make($request->all(), [
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            if ($passwordValidator->fails()) {
                return back()->withErrors($passwordValidator)->withInput();
            }

            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('user-management.index')
            ->with('success', "User '{$user->name}' updated successfully");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers() || $user->hotel_id !== $currentUser->hotel_id) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        // Prevent deleting yourself
        if ($user->id === $currentUser->id) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', "User '{$userName}' deleted successfully");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->canManageUsers() || $user->hotel_id !== $currentUser->hotel_id) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        // Prevent deactivating yourself
        if ($user->id === $currentUser->id) {
            return redirect()->route('user-management.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('user-management.index')
            ->with('success', "User '{$user->name}' {$status} successfully");
    }
} 