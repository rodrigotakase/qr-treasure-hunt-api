<?php

namespace App\Admin;

use App\Db;
use App\QrCode;
use App\Response;

class TreasureAdmin
{
    public function index(int $huntId): void
    {
        $hunt = $this->findHunt($huntId);
        $stmt = Db::connection()->prepare(
            'SELECT t.id, t.name, t.color, t.location, t.hint, COUNT(ct.user_id) AS collected_count
             FROM treasures t
             LEFT JOIN collected_treasures ct ON ct.treasure_id = t.id
             WHERE t.hunt_id = ?
             GROUP BY t.id, t.name, t.color, t.location, t.hint, t.created_at
             ORDER BY t.created_at, t.id'
        );
        $stmt->execute([$huntId]);
        View::render('treasures', $hunt['name'] . ' · Treasures', [
            'hunt' => $hunt,
            'treasures' => $stmt->fetchAll(),
        ]);
    }

    public function createForm(int $huntId): void
    {
        $hunt = $this->findHunt($huntId);
        View::render('treasure_form', 'Add treasure', [
            'heading' => 'Add treasure to ' . $hunt['name'],
            'treasure' => null,
            'action' => '/admin/hunts/' . $huntId . '/treasures',
            'cancel' => '/admin/hunts/' . $huntId . '/treasures',
        ]);
    }

    public function create(int $huntId): void
    {
        $hunt = $this->findHunt($huntId);
        $fields = $this->postedFields();
        $stmt = Db::connection()->prepare(
            'INSERT INTO treasures (id, hunt_id, name, color, location, hint) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $this->newUuid(),
            $hunt['id'],
            $fields['name'],
            $fields['color'],
            $fields['location'],
            $fields['hint'],
        ]);
        Response::redirect('/admin/hunts/' . $huntId . '/treasures');
    }

    public function editForm(string $id): void
    {
        $treasure = $this->findTreasure($id);
        View::render('treasure_form', 'Edit treasure', [
            'heading' => 'Edit ' . $treasure['name'],
            'treasure' => $treasure,
            'action' => '/admin/treasures/' . $treasure['id'] . '/update',
            'cancel' => '/admin/hunts/' . $treasure['hunt_id'] . '/treasures',
        ]);
    }

    public function update(string $id): void
    {
        $treasure = $this->findTreasure($id);
        $fields = $this->postedFields();
        $stmt = Db::connection()->prepare(
            'UPDATE treasures SET name = ?, color = ?, location = ?, hint = ? WHERE id = ?'
        );
        $stmt->execute([
            $fields['name'],
            $fields['color'],
            $fields['location'],
            $fields['hint'],
            $treasure['id'],
        ]);
        Response::redirect('/admin/hunts/' . $treasure['hunt_id'] . '/treasures');
    }

    public function confirmDelete(string $id): void
    {
        $treasure = $this->findTreasure($id);
        View::render('confirm_delete', 'Delete treasure', [
            'message' => 'Delete the treasure "' . $treasure['name'] . '"? Its QR code will stop working and its collections will be deleted too.',
            'action' => '/admin/treasures/' . $treasure['id'] . '/delete',
            'cancel' => '/admin/hunts/' . $treasure['hunt_id'] . '/treasures',
        ]);
    }

    public function delete(string $id): void
    {
        $treasure = $this->findTreasure($id);
        $stmt = Db::connection()->prepare('DELETE FROM treasures WHERE id = ?');
        $stmt->execute([$treasure['id']]);
        Response::redirect('/admin/hunts/' . $treasure['hunt_id'] . '/treasures');
    }

    public function qrPage(string $id): void
    {
        $treasure = $this->findTreasure($id);
        View::render('qr', 'QR code', [
            'treasure' => $treasure,
            'url' => QrCode::treasureUrl($treasure['id']),
        ]);
    }

    public function qrImage(string $id): void
    {
        $treasure = $this->findTreasure($id);
        $png = QrCode::treasurePng($treasure['id']);
        if (isset($_GET['download'])) {
            Response::png($png, $this->slug($treasure['name']) . '-qr.png');
        }
        Response::png($png);
    }

    private function slug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));
        if ($slug === '') {
            $slug = 'treasure';
        }
        return $slug;
    }

    private function postedFields(): array
    {
        $name = trim($_POST['name'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $hint = trim($_POST['hint'] ?? '');
        if ($name === '' || mb_strlen($name) > 100) {
            $this->reject('Treasure name must be between 1 and 100 characters.');
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
            $this->reject('Color must be a hex value like #6d28d9.');
        }
        if (mb_strlen($location) > 255 || mb_strlen($hint) > 255) {
            $this->reject('Location and hint must be at most 255 characters.');
        }
        return [
            'name' => $name,
            'color' => strtolower($color),
            'location' => $location === '' ? null : $location,
            'hint' => $hint === '' ? null : $hint,
        ];
    }

    private function reject(string $message): void
    {
        Response::html('<p>' . e($message) . '</p><p><a href="/admin">Back</a></p>', 422);
    }

    private function newUuid(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);
        $hex = bin2hex($bytes);
        return substr($hex, 0, 8) . '-'
            . substr($hex, 8, 4) . '-'
            . substr($hex, 12, 4) . '-'
            . substr($hex, 16, 4) . '-'
            . substr($hex, 20);
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

    private function findTreasure(string $id): array
    {
        $stmt = Db::connection()->prepare(
            'SELECT id, hunt_id, name, color, location, hint FROM treasures WHERE id = ?'
        );
        $stmt->execute([$id]);
        $treasure = $stmt->fetch();
        if (!$treasure) {
            Response::html('<h1>Treasure not found</h1>', 404);
        }
        return $treasure;
    }
}
