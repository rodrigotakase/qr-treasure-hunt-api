<?php

namespace App;

class Response
{
    public static function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error(string $message, int $status): void
    {
        self::json(['error' => $message], $status);
    }

    public static function html(string $body, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $body;
        exit;
    }

    public static function redirect(string $location): void
    {
        http_response_code(303);
        header('Location: ' . $location);
        exit;
    }

    public static function png(string $bytes, ?string $downloadName = null): void
    {
        http_response_code(200);
        header('Content-Type: image/png');
        header('Content-Length: ' . strlen($bytes));
        if ($downloadName !== null) {
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        }
        echo $bytes;
        exit;
    }
}
