<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Setting;
use Core\Model;
use Core\Database;

final class SettingRepository extends BaseRepository
{
    protected function getModel(): Model
    {
        return new Setting();
    }

    /**
     * Retrieve all settings as an associative key-value array.
     *
     * @return array
     */
    public function getSettings(): array
    {
        $db = Database::connection();
        $stmt = $db->query("SELECT key, value FROM cms.settings");
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[$row['key']] = $row['value'];
        }
        return $results;
    }

    /**
     * Save a setting value.
     *
     * @param string $key
     * @param string|null $value
     * @return bool
     */
    public function setSetting(string $key, ?string $value): bool
    {
        $db = Database::connection();
        $sql = "INSERT INTO cms.settings (key, value, updated_at)
                VALUES (:key, :value, CURRENT_TIMESTAMP)
                ON CONFLICT (key) DO UPDATE SET
                    value = EXCLUDED.value,
                    updated_at = EXCLUDED.updated_at";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'key' => $key,
            'value' => $value,
        ]);
    }
}
