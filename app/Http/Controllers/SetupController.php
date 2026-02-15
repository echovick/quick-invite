<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class SetupController extends Controller
{
    public function show(): Response
    {
        // Check if setup is already complete
        if (Event::exists()) {
            return Inertia::render('Admin/Login');
        }

        return Inertia::render('Setup/Index');
    }

    public function store(Request $request)
    {
        // Check if setup is already complete
        if (Event::exists()) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'admin_password' => 'required|string|min:6',
            'title' => 'required|string|max:255',
            'address' => 'required|string',
            'event_time' => 'required|date',
            'rsvp_enabled' => 'boolean',
        ]);

        Event::create([
            'title' => $validated['title'],
            'address' => $validated['address'],
            'event_time' => $validated['event_time'],
            'rsvp_enabled' => $validated['rsvp_enabled'] ?? true,
            'admin_password_hash' => Hash::make($validated['admin_password']),
        ]);

        return redirect()->route('admin.dashboard');
    }
}
