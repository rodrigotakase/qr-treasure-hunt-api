<a class="back" href="/admin">&larr; All hunts</a>
<h1><?= e($hunt['name']) ?> - leaderboard</h1>

<?php if (!$ranking): ?>
    <p class="empty">No treasures collected yet.</p>
<?php else: ?>
<div class="card table-card">
    <table>
        <thead>
            <tr>
                <th class="num">#</th>
                <th>Player</th>
                <th class="num">Collected</th>
                <th>Last collection</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($ranking as $row): ?>
            <tr>
                <td class="num"><?= (int) $row['rank'] ?></td>
                <td><?= $row['nickname'] !== null ? e($row['nickname']) : '<span class="muted">Anonymous</span>' ?></td>
                <td class="num"><?= (int) $row['collected'] ?></td>
                <td class="muted"><?= e($row['last_collected_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
