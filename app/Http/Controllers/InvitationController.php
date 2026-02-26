<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Colocation;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function send(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== auth()->id()) {
            // abort(403);
            // 
        }

        $request->validate(['email' => 'required|email']);

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email' => $request->email,
        ]);

        Mail::raw(url('/invitations/' . $invitation->token), function ($message) use ($request) {
            $message->to($request->email)->subject('Colocation Invitation');
        });

        return back()->with('success', 'Invitation sent');
    }

    public function accept($token){
        $invitation = Invitation::where('token', $token)->where('accepted', false)->firstOrFail();

        if (auth()->user()->hasActiveMembership()) {
            return redirect()->route('home')->withErrors(['error' => 'You already have an active membership']);
        }

        $invitation->colocation->members()->attach(auth()->id());
        $invitation->update(['accepted' => true]);

        return redirect()->route('colocations.show', $invitation->colocation_id)->with('success', 'Invitation accepted');
    }
}
