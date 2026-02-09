./vendor/bin/phpcs src
./vendor/bin/phpcbf src

vendor/bin/phpstan analyse src

./vendor/bin/psalm --no-cache

CREATE TABLE remember_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    selector BINARY(16) NOT NULL,
    validator_hash BINARY(32) NOT NULL,

    fingerprint BINARY(32) NOT NULL,

    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_used_at DATETIME NULL,

    CONSTRAINT uniq_selector UNIQUE (selector),
    CONSTRAINT fk_remember_tokens_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);