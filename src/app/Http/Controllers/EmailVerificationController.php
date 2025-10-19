<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'signed'])->only('verify');
        $this->middleware(['auth', 'throttle:6,1'])->only('send');
    }

    public function notice()
    {
        return auth()->user()->hasVerifiedEmail()
            ? redirect()->route('items.index')
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('items.index');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('profile.edit')->with('success', 'メール認証が完了しました。');
    }

    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('items.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
