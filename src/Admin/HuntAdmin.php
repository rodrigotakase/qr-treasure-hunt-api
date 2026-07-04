<?php

namespace App\Admin;

use App\Api\LeaderboardApi;
use App\Db;
use App\Response;

class HuntAdmin
{
    public function index(): void
    {
        $hunts = Db::connection()->query(
            'SELECT h.id, h.name, h.created_at, COUNT(t.id) AS treasure_count
             FROM hunts h
             LEFT JOIN treasures t ON t.hunt_id = h.id
             GROUP BY h.id, h.name, h.created_at
             ORDER BY h.created_at DESC, h.id DESC'
        )->fetchAll();
        View::render('hunts', 'Hunts', ['hunts' => $hunts]);
    }

    public function create(): void
    {
        $stmt = Db::connection()->prepare('INSERT INTO hunts (name) VALUES (?)');
        $stmt->execute([$this->postedName()]);
        Response::redirect('/admin');
    }

    public function update(int $id): void
    {
        $this->findHunt($id);
        $stmt = Db::connection()->prepare('UPDATE hunts SET name = ? WHERE id = ?');
        $stmt->execute([$this->postedName(), $id]);
        Response::redirect('/admin');
    }

    public function confirmDelete(int $id): void
    {
        $hunt = $this->findHunt($id);
        View::render('confirm_delete', 'Delete hunt', [
            'message' => 'Delete the hunt "' . $hunt['name'] . '"? All of its treasures and their collections will be deleted too.',
            'action' => '/admin/hunts/' . $id . '/delete',
            'cancel' => '/admin',
        ]);
    }

    public function delete(int $id): void
    {
        $this->findHunt($id);
        $stmt = Db::connection()->prepare('DELETE FROM hunts WHERE id = ?');
        $stmt->execute([$id]);
        Response::redirect('/admin');
    }

    public function leaderboard(int $id): void
    {
        $hunt = $this->findHunt($id);
        View::render('leaderboard', $hunt['name'] . ' · Leaderboard', [
            'hunt' => $hunt,
            'ranking' => LeaderboardApi::ranking($id),
        ]);
    }

    private function postedName(): string
    {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 100) {
            Response::html(
                '<p>Hunt name must be between 1 and 100 characters.</p><p><a href="/admin">Back</a></p>',
                422
            );
        }
        return $name;
    }

    private function findHunt(int $id): array
    {
        $stmt = Db::connection()->prepare('SELECT id, name FROM hunts WHERE id = ?');
        $stmt->execute([$id]);
        $hunt = $stmt->fetch();
        if (!$hunt) {
            Response::html('<h1>Hunt not found</h1>', 404);
        }
        return $hunt;
    }
}
