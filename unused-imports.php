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
    // first match all the import lines
    $matchCount = preg_match_all('/^\s*(import[\sA-Za-z0-9_\{\,\}]+from.+;)\s*$/m', $es6source, $matches);

    if ($matchCount === false) {
        trigger_error('bad regex'); // legit error handling
        return null;
    }

    if ($matchCount < 0) {
        verbose("No imports found");
        return null;
    }

    $importLines = $matches[0];
    $importCount = count($importLines);

    verbose("Found $importCount import lines");

    $importNames = [];
    $parser = new \Alex\ImportStatementParser();

    foreach ($importLines as $idx => $importCode) {
        $parser->reset($importCode);
        $importNames = array_merge($importNames, $parser->getImportIdentifiers());
    }

    $importNamesString = implode(' || ', $importNames);
    verbose("Found $importCount imports: $importNamesString");

    $unusedImports = [];
    foreach ($importNames as $importName) {
        $matchCount = preg_match_all('/\\b' . preg_quote($importName) . '\\b/', $es6source);

        if ($matchCount === false) {
            trigger_error('bad regex');
            continue;
        }

        if ($matchCount === 1) {
            $unusedImports [] = $importName;
        }
    }

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
