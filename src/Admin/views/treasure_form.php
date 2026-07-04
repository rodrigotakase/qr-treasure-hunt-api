<a class="back" href="<?= e($cancel) ?>">&larr; Back to treasures</a>
<h1><?= e($heading) ?></h1>

<form class="card form" method="post" action="<?= e($action) ?>">
    <label>Name
        <input type="text" name="name" value="<?= e($treasure['name'] ?? '') ?>" required maxlength="100">
    </label>
    <label>Color
        <input type="color" name="color" value="<?= e($treasure['color'] ?? '#6d28d9') ?>">
    </label>
    <label>Location
        <input type="text" name="location" value="<?= e($treasure['location'] ?? '') ?>" maxlength="255"
               placeholder="Where it is hidden (revealed after collecting)">
    </label>
    <label>Hint
        <input type="text" name="hint" value="<?= e($treasure['hint'] ?? '') ?>" maxlength="255"
               placeholder="Clue shown before collecting">
    </label>
    <div class="buttons">
        <button type="submit">Save treasure</button>
        <a class="button ghost" href="<?= e($cancel) ?>">Cancel</a>
    </div>
</form>
