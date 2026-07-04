<a class="back" href="/admin/hunts/<?= (int) $treasure['hunt_id'] ?>/treasures">&larr; Back to treasures</a>
<h1>QR code - <?= e($treasure['name']) ?></h1>

<div class="card qr">
    <img src="/admin/treasures/<?= e($treasure['id']) ?>/qr.png"
         alt="QR code for <?= e($treasure['name']) ?>" width="300" height="300">
    <p class="mono"><?= e($url) ?></p>
    <a class="button" href="/admin/treasures/<?= e($treasure['id']) ?>/qr.png?download=1">Download PNG</a>
</div>
