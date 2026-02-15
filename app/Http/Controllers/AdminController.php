<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invite;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $event = Event::first();

        if (!$event || !Hash::check($request->password, $event->admin_password_hash)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }

        session(['admin_authenticated' => true]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget('admin_authenticated');

        return redirect()->route('admin.login');
    }

    public function dashboard(): Response
    {
        $event = Event::first();
        $invites = Invite::where('event_id', $event->id)->get();

        $stats = [
            'total' => $invites->count(),
            'used' => $invites->where('is_used', true)->where('is_reserved', false)->count(),
            'reserved' => $invites->where('is_reserved', true)->count(),
            'remaining' => $invites->where('is_used', false)->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'event' => $event,
            'invites' => $invites,
            'stats' => $stats,
        ]);
    }

    public function createInvites(Request $request)
    {
        $validated = $request->validate([
            'count' => 'required|integer|min:1|max:1000',
            'table_start' => 'required|integer|min:1',
            'reserve_tables' => 'boolean',
            'reserved_count' => 'nullable|integer|min:0',
        ]);

        $event = Event::first();
        $tableNumber = $validated['table_start'];
        $reserveCount = $validated['reserve_tables'] ? ($validated['reserved_count'] ?? 0) : 0;

        for ($i = 0; $i < $validated['count']; $i++) {
            $isReserved = $i < $reserveCount;

            Invite::create([
                'event_id' => $event->id,
                'token' => Str::random(32),
                'table_number' => $tableNumber + $i,
                'is_reserved' => $isReserved,
                'is_used' => $isReserved, // Reserved tables are marked as used
                'invitee_name' => $isReserved ? 'Reserved' : null,
                'used_at' => $isReserved ? now() : null,
            ]);
        }

        $message = "Created {$validated['count']} invites successfully.";
        if ($reserveCount > 0) {
            $message .= " ({$reserveCount} reserved)";
        }

        return back()->with('success', $message);
    }

    public function revokeInvite(Request $request, int $inviteId)
    {
        $invite = Invite::findOrFail($inviteId);

        $invite->update([
            'is_used' => false,
            'is_reserved' => false,
            'invitee_name' => null,
            'invitee_phone' => null,
            'has_plus_one' => false,
            'used_at' => null,
        ]);

        return back()->with('success', 'Invite revoked successfully. It can now be used again.');
    }

    public function reserveTable(Request $request, int $inviteId)
    {
        $invite = Invite::findOrFail($inviteId);

        if ($invite->is_used && !$invite->is_reserved) {
            return back()->withErrors(['error' => 'This invite has already been used.']);
        }

        $invite->update([
            'is_reserved' => true,
            'is_used' => true,
            'invitee_name' => 'Reserved',
            'invitee_phone' => null,
            'has_plus_one' => false,
            'used_at' => now(),
        ]);

        return back()->with('success', 'Table reserved successfully.');
    }

    public function assignReservedTable(Request $request, int $inviteId)
    {
        $invite = Invite::with('event')->findOrFail($inviteId);

        if (!$invite->is_reserved) {
            return back()->withErrors(['error' => 'This invite is not reserved.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'has_plus_one' => 'boolean',
        ]);

        $invite->update([
            'invitee_name' => $validated['name'],
            'invitee_phone' => $validated['phone'],
            'has_plus_one' => $validated['has_plus_one'] ?? false,
            'is_reserved' => false,
            'is_used' => true,
            'used_at' => now(),
        ]);

        // Reload invite to get updated data
        $invite->refresh();

        $qrCode = $this->generateQrCode($invite);

        // Generate and download the PDF
        $pdf = Pdf::loadView('pdf.event-pass', [
            'invite' => $invite,
            'event' => $invite->event,
            'qrCode' => $qrCode,
        ]);

        return $pdf->download("event-pass-{$invite->table_number}.pdf");
    }

    public function downloadPass(int $inviteId)
    {
        $invite = Invite::with('event')->findOrFail($inviteId);

        if (!$invite->is_used) {
            abort(403, 'Invite must be used first.');
        }

        $qrCode = $this->generateQrCode($invite);

        $pdf = Pdf::loadView('pdf.event-pass', [
            'invite' => $invite,
            'event' => $invite->event,
            'qrCode' => $qrCode,
        ]);

        return $pdf->download("event-pass-{$invite->table_number}.pdf");
    }

    private function generateQrCode(Invite $invite): string
    {
        $url = route('invite.show', ['token' => $invite->token]);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCodeData = $writer->writeString($url);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCodeData);
    }
}
