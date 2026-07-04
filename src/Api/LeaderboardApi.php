<?php

namespace App\Api;

use App\Db;
use App\Response;

class LeaderboardApi
{
    public function show(int $huntId): void
    {
        $stmt = Db::connection()->prepare('SELECT id, name FROM hunts WHERE id = ?');
        $stmt->execute([$huntId]);
        $hunt = $stmt->fetch();
        if (!$hunt) {
            Response::error('Hunt not found', 404);
        }
        Response::json([
            'hunt_id' => (int) $hunt['id'],
            'hunt_name' => $hunt['name'],
            'leaderboard' => self::ranking($huntId),
        ]);
    }

    public static function ranking(int $huntId): array
    {
        $stmt = Db::connection()->prepare(
            'SELECT u.nickname, COUNT(*) AS collected, MAX(ct.collected_at) AS last_collected_at
             FROM collected_treasures ct
             JOIN treasures t ON t.id = ct.treasure_id
             JOIN users u ON u.id = ct.user_id
             WHERE t.hunt_id = ?
             GROUP BY u.id, u.nickname
             ORDER BY collected DESC, last_collected_at ASC'
        );
        $stmt->execute([$huntId]);
        $ranking = [];
        foreach ($stmt->fetchAll() as $index => $row) {
            $ranking[] = [
                'rank' => $index + 1,
                'nickname' => $row['nickname'],
                'collected' => (int) $row['collected'],
                'last_collected_at' => $row['last_collected_at'],
            ];
        }
        return $ranking;
    }
}
