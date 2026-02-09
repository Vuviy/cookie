<?php

namespace App;

use App\DTO\Coockie;
use App\Repository\CoockieRepository;
use DateTimeImmutable;

class RememberMeService
{
    public function __construct(private CoockieRepository $coockieRepository)
    {
    }

    public function createToken(int $userId): void
    {
        $fingerprint = new Fingerprint();

        $selector = random_bytes(16);
        $validator = random_bytes(32);

        $validatorHash = hash('sha256', $validator, true);

        $rememberToken = new Coockie(
            id: null,
            userId: $userId,
            selector: $selector,
            validatorHash: $validatorHash,
            fingerprint: $fingerprint->generate(),
            expiredAt: new DateTimeImmutable("+30 days"),
            createdAt: new DateTimeImmutable(),
            lastUsedAt: new DateTimeImmutable(),
        );

        $this->coockieRepository->create($rememberToken);

        $cookieValue = base64_encode($selector) . ':' . base64_encode($validator);

        setcookie(
            'remember_me',
            $cookieValue,
            [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function tryAutoLogin(): void
    {
        if (empty($_COOKIE['remember_me'])) {
            return;
        }

        $parts = explode(':', $_COOKIE['remember_me'], 2);

        if (count($parts) !== 2) {
            return;
        }

        [$selectorB64, $validatorB64] = $parts;

        $selector = base64_decode($selectorB64, true);
        $validator = base64_decode($validatorB64, true);

        if ($selector === false || strlen($selector) !== 16 || $validator === false) {
            return;
        }

        $token = $this->coockieRepository->findBySelector($selector);

        if (!$token) {
            return;
        }

        $incomingHash = hash('sha256', $validator, true);


        if (!hash_equals($token->validatorHash, $incomingHash)) {
            $this->coockieRepository->delete($token);

            setcookie('remember_me', '', time() - 3600, '/');

            return;
        }

        $fingerprint = new Fingerprint();

        if (!$fingerprint->equals($token->fingerprint)) {
            $this->coockieRepository->delete($token);
            setcookie('remember_me', '', time() - 3600, '/');
            return;
        }

        // login($token->userId);

        $newSelector = random_bytes(16);
        $newValidator = random_bytes(32);

        $newValidatorHash = hash('sha256', $newValidator, true);

        $token->selector = $newSelector;
        $token->validatorHash = $newValidatorHash;
        $token->expiredAt = new DateTimeImmutable('+30 days');
        $token->lastUsedAt = new DateTimeImmutable();

        $this->coockieRepository->update($token);

        $newCookieValue =
            base64_encode($newSelector) . ':' . base64_encode($newValidator);

        setcookie(
            'remember_me',
            $newCookieValue,
            [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    public function logout(): void
    {

        if (empty($_COOKIE['remember_me'])) {
            return;
        }

        $parts = explode(':', $_COOKIE['remember_me'], 2);

        if (count($parts) !== 2) {
            return;
        }

        [$selectorB64] = $parts;

        $selector = base64_decode($selectorB64, true);

        $token = $this->coockieRepository->findBySelector($selector);

        $this->coockieRepository->delete($token);

        setcookie('remember_me', '', time() - 3600, '/');
    }

    public function logoutAll(int $userId): void
    {
        $tokens = $this->coockieRepository->findByUserId($userId);
        foreach ($tokens as $token) {
            $this->coockieRepository->delete($token);
        }

        setcookie('remember_me', '', time() - 3600, '/');
    }
}
