<?php

$pathList[] = realpath(__DIR__ . '/../..') . '/src/*/*/cli_commands.php';
$pathList[] = realpath(__DIR__ . '/../..') . '/src/*/*/registration.php';

foreach ($pathList as $path) {
    // Sorting is disabled intentionally for performance improvement
    $files = glob($path, GLOB_NOSORT);
    if ($files === false) {
        throw new \RuntimeException('glob() returned error while searching in \'' . $path . '\'');
    }
    foreach ($files as $file) {
        include $file;
    }
}
