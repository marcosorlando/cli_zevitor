#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Helpers\InstagramToken;

require dirname(__DIR__) . '/vendor/autoload.php';

$initialToken = trim((string) ($_ENV['INSTAGRAM_ACCESS_TOKEN'] ?? ''));
if ($initialToken === '') {
    $initialToken = trim((string) (getenv('INSTAGRAM_ACCESS_TOKEN') ?: ''));
}
if ($initialToken === '') {
    fwrite(STDERR, "Configure INSTAGRAM_ACCESS_TOKEN em _app/Config/.env.\n");

    exit(1);
}

(new InstagramToken($initialToken))->get();
