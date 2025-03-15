<?php
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use function Laravel\Folio\{name, middleware, render};

name("verification.verify");
middleware(["auth", "signed", "throttle:6,1"]);

render(function (EmailVerificationRequest $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return redirect()->intended(route("home", absolute: false) . "?verified=1");
    }

    if ($request->user()->markEmailAsVerified()) {
        /** @var \Illuminate\Contracts\Auth\MustVerifyEmail $user */
        $user = $request->user();

        event(new Verified($user));
    }

    return redirect()->intended(route("home", absolute: false) . "?verified=1");
});

?>
