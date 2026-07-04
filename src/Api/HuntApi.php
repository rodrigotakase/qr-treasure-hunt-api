<?php

namespace App\Api;

use App\Db;
use App\Response;

class HuntApi
{
    public function treasures(int $huntId): void
    {
        $pdo = Db::connection();
        $stmt = $pdo->prepare('SELECT id, name FROM hunts WHERE id = ?');
        $stmt->execute([$huntId]);
        $hunt = $stmt->fetch();
        if (!$hunt) {
            Response::error('Hunt not found', 404);
        }
        $userId = $_GET['user_id'] ?? '';
        if (!is_string($userId)) {
            $userId = '';
        }
        $stmt = $pdo->prepare(
            'SELECT t.id, t.name, t.color, t.hint, t.location, ct.user_id AS collector
             FROM treasures t
             LEFT JOIN collected_treasures ct ON ct.treasure_id = t.id AND ct.user_id = ?
             WHERE t.hunt_id = ?
             ORDER BY t.created_at, t.id'
        );
        $stmt->execute([$userId, $huntId]);
        $treasures = [];
        foreach ($stmt->fetchAll() as $row) {
            $collected = $row['collector'] !== null;
            $treasure = [
                'name' => $row['name'],
                'color' => $row['color'],
                'hint' => $row['hint'],
                'collected' => $collected,
            ];
            if ($collected) {
                $treasure['id'] = $row['id'];
                $treasure['location'] = $row['location'];
            }
            $treasures[] = $treasure;
        }
        Response::json([
            'hunt_id' => (int) $hunt['id'],
            'hunt_name' => $hunt['name'],
            'treasures' => $treasures,
        ]);
    }
}
