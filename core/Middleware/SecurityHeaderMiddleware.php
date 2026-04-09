<?php

namespace Core\Middleware;

use Core\Middleware\Middleware;
use Core\Support\DebugHelper;

class SecurityHeaderMiddleware extends Middleware
{
    public function handle(): bool
    {
        /**
         * --------------------------------------------------------------------------------------------
         *  ---- Content Security Policy (CSP) ----
         * --------------------------------------------------------------------------------------------
         * 
         * default-src 'self' → seules les ressources venant de ton domaine sont autorisées.
         * script-src 'self' → seuls tes JS locaux peuvent s’exécuter (bloque les <script> injectés).
         * script-src 'self' https://js.stripe.com → autorise Stripe.js (paiement sécurisé).
         * style-src 'self' 'unsafe-inline' → autorise tes CSS + styles inline (optionnel, tu peux l’enlever si tu interdis les inline styles).
         * style-src ... https://fonts.googleapis.com → autorise Google Fonts.
         * font-src ... https://fonts.gstatic.com → autorise le chargement réel des polices
         * img-src 'self' data: → autorise images locales + data URI (base64).
         * object-src 'none' → n’autorises pas d’<object> depuis l’extérieur
         * frame-src 'self' → n’autorises pas d’<iframe> depuis l’extérieur
         * frame-ancestors 'none' → interdit l’intégration de ton site dans des iframes (protection clickjacking).
         * report-to /security/log-csp → Logger les violations de sécurité (via accessLogger)
         */
        $cspDirectives = [
            "frame-src 'self' https://www.google.com https://maps.google.com",
            "default-src 'self'",
            "font-src 'self' https://fonts.gstatic.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "img-src 'self' data:",
            "script-src 'self' https://js.stripe.com",
            "object-src 'none'",
            "frame-ancestors 'none'"
        ];
        header("Content-Security-Policy: " . implode("; ", $cspDirectives));

        // ---- Autres headers de sécurité ----

        // Interdit l'affichage dans un iframe (anti-clickjacking)
        header("X-Frame-Options: DENY");

        // Active la protection XSS des navigateurs (utile pour les anciens)
        header("X-XSS-Protection: 1; mode=block");

        // Empêche le MIME sniffing (force le respect du Content-Type)
        header("X-Content-Type-Options: nosniff");

        // Enforce Referrer-Policy pour éviter les fuites d'URL
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Ajoute un header "Permissions-Policy" pour désactiver certaines APIs navigateur
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

        // Strict-Transport-Security (HSTS) : force HTTPS pour 1 an
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }
        return true;
    }
}
