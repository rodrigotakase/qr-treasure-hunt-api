<?php

namespace App\Api;

use App\Db;
use App\Response;

class CollectApi
{
    private const UUID_PATTERN = '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/';

    public function collect(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!is_array($body)) {
            Response::error('Request body must be a JSON object', 400);
        }
        $userId = $body['user_id'] ?? null;
        $treasureId = $body['treasure_id'] ?? null;
        if (!is_string($userId) || !preg_match(self::UUID_PATTERN, $userId)) {
            Response::error('user_id must be a UUID', 422);
        }
        if (!is_string($treasureId) || !preg_match(self::UUID_PATTERN, $treasureId)) {
            Response::error('treasure_id must be a UUID', 422);
        }

        $pdo = Db::connection();
        $stmt = $pdo->prepare(
            'SELECT t.id, t.name, t.color, t.hint, t.location, t.hunt_id, h.name AS hunt_name
             FROM treasures t
             JOIN hunts h ON h.id = t.hunt_id
             WHERE t.id = ?'
        );
        $stmt->execute([$treasureId]);
        $treasure = $stmt->fetch();
        if (!$treasure) {
            Response::error('Treasure not found', 404);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO users (id, nickname) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE nickname = COALESCE(VALUES(nickname), nickname)'
        );
        $stmt->execute([$userId, $this->cleanNickname($body['nickname'] ?? null)]);

        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO collected_treasures (user_id, treasure_id) VALUES (?, ?)'
        );
        $stmt->execute([$userId, $treasureId]);
        $alreadyCollected = $stmt->rowCount() === 0;

        $stmt = $pdo->prepare(
            'SELECT collected_at FROM collected_treasures WHERE user_id = ? AND treasure_id = ?'
        );
        $stmt->execute([$userId, $treasureId]);

        Response::json([
            'alreadyCollected' => $alreadyCollected,
            'collected_at' => $stmt->fetchColumn(),
            'treasure' => [
                'id' => $treasure['id'],
                'name' => $treasure['name'],
                'color' => $treasure['color'],
                'hint' => $treasure['hint'],
                'location' => $treasure['location'],
                'hunt_id' => (int) $treasure['hunt_id'],
                'hunt_name' => $treasure['hunt_name'],
            ],
        ], $alreadyCollected ? 200 : 201);
    }

    private function cleanNickname($nickname): ?string
    {
        if (!is_string($nickname)) {
            return null;
        }
        $nickname = trim($nickname);
        if ($nickname === '') {
            return null;
        }
        return mb_substr($nickname, 0, 50);
    }
}
