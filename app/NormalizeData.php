<?php

namespace App;

trait NormalizeData
{
    /**
     * Telefonszám normalizálása az E.164 szabvány szerint.
     *
     * @param string $phoneNumber
     * @return string
     */
    public function normalizePhoneNumber(string $phoneNumber): string
    {
        // Csak számokat és "+" jelet hagy meg
        $normalized = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Ha nem tartalmaz "+" jelet, hozzáadjuk a magyar országkódot
        if (!str_starts_with($normalized, '+')) {
            // Check if the number starts with '06' (Hungarian domestic format)
            if (str_starts_with($normalized, '06')) {
                $normalized = '+36' . substr($normalized, 2); // Replace '06' with '+36'
            } else {
                $normalized = '+36' . ltrim($normalized, '0'); // Default case
            }
        }

        return $normalized;
    }


    /**
     * E-mail cím normalizálása (kisbetűsítés, címkék eltávolítása).
     *
     * @param string $email
     * @return string
     */
    public function normalizeEmail(string $email): string
    {
        // Szétválasztjuk a helyi részt és a domaint
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            throw new InvalidArgumentException('Érvénytelen e-mail cím formátum.');
        }

        $localPart = $parts[0];
        $domainPart = $parts[1];

        // A helyi részből eltávolítjuk a "+" utáni részt
        if (strpos($localPart, '+') !== false) {
            $localPart = substr($localPart, 0, strpos($localPart, '+'));
        }

        // Az egész címet kisbetűssé alakítjuk
        $normalizedEmail = strtolower($localPart . '@' . $domainPart);

        return $normalizedEmail;
    }
}
