<?php

namespace App\Api;

use App\Db;
use App\Response;

class TreasureApi
{
    public function show(string $id): void
    {
        $stmt = Db::connection()->prepare(
            'SELECT t.id, t.name, t.color, t.hint, t.location, t.hunt_id, h.name AS hunt_name
             FROM treasures t
             JOIN hunts h ON h.id = t.hunt_id
             WHERE t.id = ?'
        );
        $stmt->execute([$id]);
        $treasure = $stmt->fetch();
        if (!$treasure) {
            Response::error('Treasure not found', 404);
        }
        $userId = $_GET['user_id'] ?? '';
        if (!is_string($userId)) {
            $userId = '';
        }
        $collected = $this->isCollectedBy($id, $userId);
        $data = [
            'id' => $treasure['id'],
            'name' => $treasure['name'],
            'color' => $treasure['color'],
            'hint' => $treasure['hint'],
            'hunt_id' => (int) $treasure['hunt_id'],
            'hunt_name' => $treasure['hunt_name'],
            'collected' => $collected,
        ];
        if ($collected) {
            $data['location'] = $treasure['location'];
        }
        Response::json($data);
    }

    private function isCollectedBy(string $treasureId, string $userId): bool
    {
        if ($userId === '') {
            return false;
        }
        $stmt = Db::connection()->prepare(
            'SELECT 1 FROM collected_treasures WHERE treasure_id = ? AND user_id = ?'
        );
        $stmt->execute([$treasureId, $userId]);
        return (bool) $stmt->fetchColumn();
    }
}
