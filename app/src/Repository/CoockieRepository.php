<?php

namespace App\Repository;

use App\Database\Database;
use App\DTO\Coockie;
use DateTimeImmutable;

final class CoockieRepository
{
    public function __construct(private Database $db)
    {
    }

    public function findByUserId(int $userId): array
    {
        $row = $this->db
            ->table('remember_tokens')
            ->where('user_id', '=', $userId)
            ->get();

        if (0 === count($row)) {
            return [];
        }

        $coockies = [];

        foreach ($row as $item) {
            $coockies[] = new Coockie(
                id: (int)$item['id'],
                userId: $item['user_id'] ? (int)$item['user_id'] : null,
                selector: $item['selector'],
                validatorHash: $item['validator_hash'],
                fingerprint: $item['fingerprint'],
                expiredAt: new DateTimeImmutable($item['expires_at']),
                createdAt: new DateTimeImmutable($item['created_at']),
                lastUsedAt: new DateTimeImmutable($item['last_used_at']),
            );
        }
        return $coockies;
    }


    public function findBySelector(string $selector): ?Coockie
    {
        $row = $this->db
            ->table('remember_tokens')
            ->where('selector', '=', $selector)
            ->first();

        if (!$row) {
            return null;
        }

        return new Coockie(
            id: (int)$row['id'],
            userId: $row['user_id'] ? (int)$row['user_id'] : null,
            selector: $row['selector'],
            validatorHash: $row['validator_hash'],
            fingerprint: $row['fingerprint'],
            expiredAt: new DateTimeImmutable($row['expires_at']),
            createdAt: new DateTimeImmutable($row['created_at']),
            lastUsedAt: new DateTimeImmutable($row['last_used_at']),
        );
    }

    public function create(Coockie $rememberToken): void
    {
        $this->db->table('remember_tokens')->insert([
            'user_id' => $rememberToken->userId,
            'selector' => $rememberToken->selector,
            'validator_hash' => $rememberToken->validatorHash,
            'fingerprint' => $rememberToken->fingerprint,
            'expires_at' => $rememberToken->expiredAt->format('Y-m-d H:i:s'),
            'created_at' => $rememberToken->createdAt->format('Y-m-d H:i:s'),
            'last_used_at' => $rememberToken->lastUsedAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(Coockie $rememberToken): void
    {
        $this->db->table('remember_tokens')
            ->where('user_id', '=', $rememberToken->userId)
            ->update([
                'selector' => $rememberToken->selector,
                'validator_hash' => $rememberToken->validatorHash,
                'fingerprint' => $rememberToken->fingerprint,
                'last_used_at' => $rememberToken->lastUsedAt->format('Y-m-d H:i:s'),
            ]);
    }

    public function delete(Coockie $rememberToken): void
    {
        $this->db->table('remember_tokens')
            ->where('id', '=', $rememberToken->id)
            ->delete();
    }
}
