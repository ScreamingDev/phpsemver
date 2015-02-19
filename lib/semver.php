<?php

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'UTC');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$softecApplication = new \Semver\Console\Application();
$softecApplication->fetchCommands();

$softecApplication->run();