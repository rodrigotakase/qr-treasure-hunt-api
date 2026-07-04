<h1>Are you sure?</h1>

<div class="card">
    <p><?= e($message) ?></p>
    <form class="inline-form" method="post" action="<?= e($action) ?>">
        <button type="submit" class="danger">Yes, delete</button>
        <a class="button ghost" href="<?= e($cancel) ?>">Cancel</a>
    </form>
</div>
