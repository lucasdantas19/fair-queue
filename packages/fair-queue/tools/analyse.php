<?php

declare(strict_types=1);

$sourceDirectory = dirname(__DIR__).'/src';
$files = [];

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDirectory)) as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

sort($files);
$failures = [];

foreach ($files as $file) {
    $command = PHP_BINARY.' -l '.escapeshellarg($file);
    exec($command, $output, $exitCode);

    if ($exitCode !== 0) {
        $failures[] = "Syntax error: {$file}";
    }

    $contents = file_get_contents($file);

    if (preg_match('/(?:namespace|use|new|extends|implements|::)[^;]*\\bApp\\\\/', $contents) === 1) {
        $failures[] = "Application dependency found: {$file}";
    }
}

if ($failures !== []) {
    fwrite(STDERR, implode(PHP_EOL, $failures).PHP_EOL);
    exit(1);
}

echo 'Package structural analysis passed: '.count($files).' source files checked.'.PHP_EOL;
