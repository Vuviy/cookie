<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;
use App\DTO\Cookie;
use DateTimeImmutable;

final class CookieRepository
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
            $coockies[] = new Cookie(
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


    public function findBySelector(string $selector): ?Cookie
    {
        $row = $this->db
            ->table('remember_tokens')
            ->where('selector', '=', $selector)
            ->first();

        if (!$row) {
            return null;
        }

        return new Cookie(
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

    public function create(Cookie $rememberToken): void
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

    public function update(Cookie $rememberToken): void
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

    public function delete(Cookie $rememberToken): void
    {
        $this->db->table('remember_tokens')
            ->where('id', '=', $rememberToken->id)
            ->delete();
    }
}
