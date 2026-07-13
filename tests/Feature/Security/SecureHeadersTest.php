<?php

use Illuminate\Http\Request;
use Modules\Security\Middleware\SecureHeaders;

/**
 * Drives the middleware directly so the CSP outcome depends only on the Vite
 * hot file, not on how the test kernel wires middleware groups.
 */
function cspFor(?string $hotServer): string
{
    $hotFile = public_path('hot');
    $backup = $hotFile.'.testbak';
    $restore = false;

    if (file_exists($hotFile)) {
        rename($hotFile, $backup);
        $restore = true;
    }

    if ($hotServer !== null) {
        file_put_contents($hotFile, $hotServer);
    }

    try {
        $response = (new SecureHeaders)->handle(
            Request::create('/dashboard', 'GET'),
            fn () => response('ok'),
        );

        return (string) $response->headers->get('Content-Security-Policy');
    } finally {
        if (file_exists($hotFile)) {
            unlink($hotFile);
        }

        if ($restore) {
            rename($backup, $hotFile);
        }
    }
}

it('sends a locked-down CSP when Vite is not running hot', function () {
    $csp = cspFor(null);

    expect($csp)->toContain("script-src 'self'");
    expect($csp)->not->toContain('127.0.0.1');
    expect($csp)->not->toContain("'unsafe-eval'");
});

it('sets the other security headers', function () {
    $headers = (new SecureHeaders)->handle(
        Request::create('/dashboard', 'GET'),
        fn () => response('ok'),
    )->headers;

    expect($headers->get('X-Frame-Options'))->toBe('DENY');
    expect($headers->get('X-Content-Type-Options'))->toBe('nosniff');
    expect($headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin');
});

it('relaxes the CSP to allow the Vite dev server when running hot', function () {
    $csp = cspFor('http://localhost:5173');

    expect($csp)->toContain('http://localhost:5173');
    expect($csp)->toContain('ws://localhost:5173');
    expect($csp)->toContain("'unsafe-eval'");
});
