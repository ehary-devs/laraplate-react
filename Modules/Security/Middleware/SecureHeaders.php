<?php

namespace Modules\Security\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        $response = $next($request);

        if (! config('security.headers.enabled', true)) {
            return $response;
        }

        $nonce = Vite::cspNonce();

        $response->headers->set('Content-Security-Policy', $this->buildCsp($nonce));
        $response->headers->set('X-Frame-Options', config('security.headers.x_frame_options', 'DENY'));
        $response->headers->set('X-Content-Type-Options', config('security.headers.x_content_type_options', 'nosniff'));
        $response->headers->set('Referrer-Policy', config('security.headers.referrer_policy', 'strict-origin-when-cross-origin'));
        $response->headers->set('Permissions-Policy', config('security.headers.permissions_policy', 'camera=(), microphone=(), geolocation=()'));

        if (app()->isProduction()) {
            $response->headers->set('Strict-Transport-Security', config('security.headers.hsts', 'max-age=31536000; includeSubDomains'));
        }

        return $response;
    }

    private function buildCsp(string $nonce): string
    {
        $base = (string) config('security.headers.content_security_policy');

        // In dev, the Vite server serves assets, HMR scripts, styles and fonts
        // from a separate origin. Relax the policy to allow it rather than
        // weakening the production policy.
        if (Vite::isRunningHot()) {
            return $this->buildDevCsp($base);
        }

        if ($nonce === '') {
            return $base;
        }

        // Inject nonce into the script-src directive
        return (string) preg_replace(
            '/script-src ([^;]*)/',
            "script-src $1 'nonce-{$nonce}'",
            $base,
        );
    }

    /**
     * Rewrite the CSP to permit the running Vite dev server (its HTTP origin
     * for scripts/styles/fonts and its websocket for HMR). Dev-only.
     */
    private function buildDevCsp(string $base): string
    {
        $devServer = trim((string) @file_get_contents(Vite::hotFile()));

        if ($devServer === '') {
            return $base;
        }

        $ws = (string) preg_replace('/^http/', 'ws', $devServer);

        $additions = [
            'script-src' => "{$devServer} 'unsafe-inline' 'unsafe-eval'",
            'style-src' => $devServer,
            'font-src' => $devServer,
            'connect-src' => "{$devServer} {$ws}",
        ];

        foreach ($additions as $directive => $extra) {
            $base = (string) preg_replace(
                "/{$directive} ([^;]*)/",
                "{$directive} $1 {$extra}",
                $base,
            );
        }

        return $base;
    }
}
