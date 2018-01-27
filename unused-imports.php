<?php
require __DIR__ . '/vendor/autoload.php';

// echos unused imports one file at a time as we find them. no output for good files

error_reporting(E_ALL);
define('OUTPUT', 'BASIC');

$rootDir = $argv[1] ?? null;

if (!is_string($rootDir) || !is_dir($rootDir)) {
    fwrite(STDERR, "Look, I'm not magic. You should pass a valid directory"); //output message into 2> buffer
    exit(1); //return error status code to shell
}

$unusedImportGenerator = new Alex\UnusedImportGenerator();

$generator = $unusedImportGenerator->generateUnusedImportIdentifiers($rootDir);

$fileCount = 0;
$unusedImportCount = 0;
$unusedImportsByFile = [];

foreach ($generator as $item) {
    $filename = $item['filename'];
    $unusedImports = $item['unusedIdentifiers'];

    if (OUTPUT === 'JSON') {
        $unusedImportsByFile[$filename] = $unusedImports;
    }

    if (OUTPUT === 'BASIC') {
        $unusedImportsString = implode(', ', $unusedImports);
        echo "$filename > $unusedImportsString\n";
    }

    $fileCount++;
    $unusedImportCount += count($unusedImports);
}

switch (OUTPUT) {
    case 'JSON':
        echo json_encode([
            'unusedImports' => $unusedImportsByFile,
            'totalUnusedFiles' => $fileCount,
            'totalUnusedIdentifiers' => $unusedImportCount,
        ]);
        break;
    case 'BASIC':
        echo "\nTotal number of files with unused imports: $fileCount\n";
        echo "Total number of unused imports from all files: $unusedImportCount\n";
        break;
}

exit(0);
