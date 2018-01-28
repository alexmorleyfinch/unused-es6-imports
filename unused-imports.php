<?php

require __DIR__ . '/vendor/autoload.php';

// requires -f arg as filename and optional -j to enable json output
$options = getopt('f:j::');
$rootDir = $options['f'] ?? null;
$useJson = ($options['j'] ?? null) === false;

if (!is_string($rootDir) || !is_dir($rootDir)) {
    fwrite(STDERR, "Look, I'm not magic. You should pass a valid directory");
    exit(1);
}

$container = new Almofi\UnusedEs6Imports\Container();

$unusedImportsRunner = $container->getUnusedImportRunner($rootDir);

if ($useJson) {
    $jsonData = $unusedImportsRunner->synchronousOutput();

    echo json_encode($jsonData), "\n";
} else {
    $unusedImportsRunner->streamOutput(STDOUT, function($result) {
        return $result['filename'] . ' > ' . implode(', ', $result['unusedIdentifiers']);
    });
}

exit(0);
