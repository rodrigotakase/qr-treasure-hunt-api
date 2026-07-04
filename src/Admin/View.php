<?php

namespace App\Admin;

use App\Response;

class View
{
    public static function render(string $view, string $title, array $data = []): void
    {
        extract($data);
        ob_start();
        require __DIR__ . '/views/' . $view . '.php';
        $content = ob_get_clean();
        ob_start();
        require __DIR__ . '/views/layout.php';
        Response::html(ob_get_clean());
    }
}
