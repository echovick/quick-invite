<?php

namespace App\Http\Controllers;

use App\Models\Invite;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    public function show(string $token): Response
    {
        $invite = Invite::with('event')->where('token', $token)->firstOrFail();

        if ($invite->is_used) {
            return Inertia::render('Invite/AlreadyUsed', [
                'invite' => $invite,
                'event' => $invite->event,
            ]);
        }

        return Inertia::render('Invite/Redeem', [
            'invite' => $invite,
            'event' => $invite->event,
        ]);
    }

    public function redeem(Request $request, string $token)
    {
        $invite = Invite::with('event')->where('token', $token)->firstOrFail();

        if ($invite->is_used) {
            return back()->withErrors([
                'error' => 'This invite has already been used.',
            ]);
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
            'is_used' => true,
            'used_at' => now(),
        ]);

        // Get a fresh instance from database
        $freshInvite = Invite::with('event')->find($invite->id);

        $inviteData = [
            'id' => $freshInvite->id,
            'token' => $freshInvite->token,
            'table_number' => $freshInvite->table_number,
            'invitee_name' => $freshInvite->invitee_name,
            'has_plus_one' => (bool) $freshInvite->has_plus_one,
        ];

        $eventData = [
            'id' => $freshInvite->event->id,
            'title' => $freshInvite->event->title,
            'address' => $freshInvite->event->address,
            'event_time' => $freshInvite->event->event_time,
        ];

        \Log::info('Success page data:', [
            'invite' => $inviteData,
            'event' => $eventData,
        ]);

        return Inertia::render('Invite/Success', [
            'invite' => $inviteData,
            'event' => $eventData,
        ]);
    }

    public function downloadPass(string $token)
    {
        $invite = Invite::with('event')->where('token', $token)->firstOrFail();

        if (!$invite->is_used) {
            abort(403, 'Invite must be redeemed first.');
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
