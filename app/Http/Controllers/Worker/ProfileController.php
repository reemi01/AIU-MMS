<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $worker = auth()->user()->worker;

        return view('worker.profile', compact('worker'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20|regex:/^[0-9\-\+\(\)\s]+$/',
            'dob' => 'nullable|date|before:today|after:1940-01-01',
            'notes' => 'nullable|string|max:500',
            'avatar' => 'nullable|string',
        ], [
            'name.required' => 'Full name is required',
            'name.min' => 'Name must be at least 2 characters',
            'email.email' => 'Please enter a valid email address',
            'phone.regex' => 'Please enter a valid phone number',
            'dob.before' => 'Date of birth must be in the past',
            'dob.after' => 'Please enter a valid date of birth',
            'notes.max' => 'Notes cannot exceed 500 characters',
        ]);

        $user = auth()->user();
        $worker = $user->worker;

        $user->update([
            'name' => $validated['name'],
        ]);

        $worker->update([
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'dob' => $validated['dob'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
        ]);

        return redirect()->route('worker.profile')
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
            return redirect()->route('worker.profile')
                ->withErrors(['current_password' => 'Current password is incorrect'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('worker.profile')
            ->with('success', 'Password changed successfully!');
    }
}
