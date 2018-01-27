<?php

namespace App;

class UnusedImportGenerator
{
    private $unusedDetector;
    private $filenameGenerator;

    public function __construct()
    {
        $this->filenameGenerator = new FilenameGenerator('/\.jsx?$/', true);
        $this->unusedDetector = new UnusedEs6Detector();
    }

    public function generateUnusedImportIdentifiers($rootDir)
    {
        $generator = $this->filenameGenerator->recurseFiles($rootDir);

        foreach ($generator as $filename) {
            $es6source = file_get_contents($filename);

            if ($es6source === false) {
                trigger_error("Could not get contents $filename");
                continue;
            }

            // TODO will eventually contain info about line numbers so we can remove the unused identifiers
            $importStatements = $this->unusedDetector->matchImportStatements($es6source);

            if (!$importStatements) {
                continue;
            }

            $importNames = $this->unusedDetector->getImportIdentifiers($importStatements);

            $unusedImports = $this->unusedDetector->getUnusedIdentifiers($es6source, $importNames);

            if (empty($unusedImports)) {
                continue;
            }

            // TODO make this an object representation
            yield ['filename' => $filename, 'unusedIdentifiers' => $unusedImports];
        }
    }
}