CREATE TABLE IF NOT EXISTS `images` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `day` INT unsigned NOT NULL,
    `photo_id` INT unsigned NOT NULL,
    `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`day`),
    UNIQUE (`photo_id`)
);

CREATE TABLE IF NOT EXISTS `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `email` TEXT NOT NULL,
    `password_hash` TEXT NOT NULL,
    `last_login` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`email`)
);
