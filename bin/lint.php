<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
$paths = [
    $root . '/public',
    $root . '/src',
    $root . '/bin',
];

$failed = 0;
foreach ($paths as $path) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname());
        passthru($command, $code);
        if ($code !== 0) {
            $failed++;
        }
    }
}

exit($failed > 0 ? 1 : 0);
