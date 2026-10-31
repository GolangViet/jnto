<?php

declare(strict_types=1);

use Core\Database;

return new class {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = Database::connection();

        $db->exec("
            CREATE TABLE IF NOT EXISTS cms.guest_survey_submissions (
                id BIGSERIAL PRIMARY KEY,
                guest_token VARCHAR(64) NOT NULL UNIQUE,
                quiz_id BIGINT NOT NULL,
                travel_status VARCHAR(50) NULL,
                answers_payload JSONB NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                expires_at TIMESTAMP NULL
            );
            CREATE INDEX IF NOT EXISTS idx_guest_survey_token ON cms.guest_survey_submissions(guest_token);
            CREATE INDEX IF NOT EXISTS idx_guest_survey_created_at ON cms.guest_survey_submissions(created_at);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $db = Database::connection();
        $db->exec("DROP TABLE IF EXISTS cms.guest_survey_submissions CASCADE");
    }
};

