<?php

namespace App\DTO;

final class Coockie
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $selector,
        public string $validatorHash,
        public string $fingerprint,
        public \DateTimeImmutable $expiredAt,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $lastUsedAt,
    ) {
    }
}
