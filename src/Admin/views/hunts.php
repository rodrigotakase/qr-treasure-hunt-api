<h1>Hunts</h1>

<form class="card inline-form" method="post" action="/admin/hunts">
    <input type="text" name="name" placeholder="New hunt name" required maxlength="100">
    <button type="submit">Create hunt</button>
</form>

<?php if (!$hunts): ?>
    <p class="empty">No hunts yet. Create the first one above.</p>
<?php else: ?>
<div class="card table-card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th class="num">Treasures</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($hunts as $hunt): ?>
            <tr>
                <td>
                    <form class="rename-form" method="post" action="/admin/hunts/<?= (int) $hunt['id'] ?>/update">
                        <input type="text" name="name" value="<?= e($hunt['name']) ?>" required maxlength="100">
                        <button type="submit" class="ghost">Rename</button>
                    </form>
                </td>
                <td class="num"><?= (int) $hunt['treasure_count'] ?></td>
                <td class="muted"><?= e($hunt['created_at']) ?></td>
                <td class="actions">
                    <a class="button" href="/admin/hunts/<?= (int) $hunt['id'] ?>/treasures">Treasures</a>
                    <a class="button ghost" href="/admin/hunts/<?= (int) $hunt['id'] ?>/leaderboard">Leaderboard</a>
                    <a class="button danger" href="/admin/hunts/<?= (int) $hunt['id'] ?>/delete">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
