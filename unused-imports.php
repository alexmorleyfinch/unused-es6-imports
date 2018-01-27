<?php
require __DIR__ . '/vendor/autoload.php';

// echos unused imports one file at a time as we find them. no output for good files

error_reporting(E_ALL);
define('VERBOSE', !true);

$rootDir = $argv[1] ?? null;

if (!is_string($rootDir) || !is_dir($rootDir)) {
    exit("Look, I'm not magic. You should pass a valid directory");
}

$filenameGenerator = new Alex\FilenameGenerator('/\.jsx?$/', true);

$generator = $filenameGenerator->recurseFiles($rootDir);

$fileCount = 0;
$unusedImportCount = 0;

foreach ($generator as $filename) {

    verbose("Testing $filename.");

    $es6source = file_get_contents($filename);

    if ($es6source === false) {
        trigger_error("Could not get contents $filename");
        continue;
    }

    $unusedImports = getUnusedImports($es6source);

    if (!$unusedImports) {
        continue;
    }

    $fileCount++;
    $unusedImportCount += count($unusedImports);

    $unusedImportsString = implode(', ', $unusedImports);
    echo "$filename > $unusedImportsString\n";
}

echo "\nTotal number of files with unused imports: $fileCount\n";
echo "Total number of unused imports from all files: $unusedImportCount\n";

exit(0);

// returns array of unused import strings, or null if none could be found
function getUnusedImports($es6source)
{
    $unusedDetector = new Alex\UnusedEs6Detector();

    $importStatements = $unusedDetector->matchImportStatements($es6source);

    if (!$importStatements) {
        verbose("No imports found");
    }

    $importCount = count($importStatements);
    verbose("Found $importCount import lines");

    $importNames = $unusedDetector->getImportIdentifiers($importStatements);

    $importNamesString = implode(' || ', $importNames);
    verbose("Found $importCount imports: $importNamesString");

    $unusedImports = $unusedDetector->getUnusedIdentifiers($es6source, $importNames);

    if (empty($unusedImports)) {
        verbose('All imports used');
        return null;
    }

    return $unusedImports;
}

function verbose($msg)
{
    if (VERBOSE) echo $msg, "\n";
}
