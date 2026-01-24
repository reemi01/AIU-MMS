<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'username' => 'required|string|min:3|max:255|alpha_dash|unique:users,username,'.auth()->id(),
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|string',
        ], [
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters',
            'username.required' => 'Username is required',
            'username.min' => 'Username must be at least 3 characters',
            'username.unique' => 'This username is already taken',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores',
            'email.email' => 'Please enter a valid email address',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'avatar' => $validated['avatar'] ?? $user->avatar,
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'New password must be at least 6 characters',
            'new_password.confirmed' => 'New password confirmation does not match',
        ]);

        $user = auth()->user();

        // Verify current password
        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('admin.profile.index')
                ->withErrors(['current_password' => 'Current password is incorrect'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('admin.profile.index')
            ->with('success', 'Password changed successfully!');
    }
}
