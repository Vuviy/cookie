<?php

namespace App;

final class Fingerprint
{
    public function generate(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        return hash('sha256', $userAgent);
    }

    public function equals(string $storedFingerprint): bool
    {
        return hash_equals($storedFingerprint, $this->generate());
    }
}
