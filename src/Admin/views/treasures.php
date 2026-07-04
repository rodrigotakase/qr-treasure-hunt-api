<a class="back" href="/admin">&larr; All hunts</a>
<h1><?= e($hunt['name']) ?> - treasures</h1>

<div class="toolbar">
    <a class="button" href="/admin/hunts/<?= (int) $hunt['id'] ?>/treasures/new">Add treasure</a>
    <a class="button ghost" href="/admin/hunts/<?= (int) $hunt['id'] ?>/leaderboard">Leaderboard</a>
</div>

<?php if (!$treasures): ?>
    <p class="empty">No treasures yet. Add the first one.</p>
<?php else: ?>
<div class="card table-card">
    <table>
        <thead>
            <tr>
                <th>Treasure</th>
                <th>Location</th>
                <th>Hint</th>
                <th class="num">Collected by</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($treasures as $treasure): ?>
            <tr>
                <td>
                    <span class="swatch" style="background: <?= e($treasure['color']) ?>"></span>
                    <?= e($treasure['name']) ?>
                </td>
                <td class="muted"><?= e($treasure['location']) ?></td>
                <td class="muted"><?= e($treasure['hint']) ?></td>
                <td class="num"><?= (int) $treasure['collected_count'] ?></td>
                <td class="actions">
                    <a class="button" href="/admin/treasures/<?= e($treasure['id']) ?>/qr">QR</a>
                    <a class="button ghost" href="/admin/treasures/<?= e($treasure['id']) ?>/edit">Edit</a>
                    <a class="button danger" href="/admin/treasures/<?= e($treasure['id']) ?>/delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
