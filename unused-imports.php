<?php

require __DIR__ . '/vendor/autoload.php';

$options = getopt('f:j::');
$rootDir = $options['f'] ?? null;
$useJson = ($options['j'] ?? null) === false;

if (!is_string($rootDir) || !is_dir($rootDir)) {
    fwrite(STDERR, "Look, I'm not magic. You should pass a valid directory");
    exit(1); //return error status code to shell
}

$unusedImportsRunner = new App\UnusedImportRunner($rootDir);

if ($useJson) {
    $jsonData = $unusedImportsRunner->synchronousOutput();

    echo json_encode($jsonData);
} else {
    $unusedImportsRunner->streamOutput(STDOUT, function($result) {
        $unusedImportsString = implode(', ', $result['unusedIdentifiers']);

        return "{$result['filename']} > $unusedImportsString";
    });
}

exit(0);
