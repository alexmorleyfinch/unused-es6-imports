<?php

namespace Almofi\UnusedEs6Imports\App;

class UnusedImportRunner
{
    private $generator;

    public function __construct(string $rootDir)
    {
        $this->reset($rootDir);
    }

    public function reset(string $rootDir)
    {
        $unusedImportGenerator = new UnusedImportGenerator();

        $this->generator = $unusedImportGenerator->generateUnusedImportIdentifiers($rootDir);
    }

    public function streamOutput($stream, callable $toString)
    {
        $fileCount = 0;
        $unusedImportCount = 0;

        foreach ($this->generator as $result) {
            $fileCount++;
            $unusedImportCount += count($result['unusedIdentifiers']);

            fwrite($stream, $toString($result) . "\n");
        }

        fwrite($stream, "\nTotal number of files with unused imports: $fileCount\n");
        fwrite($stream,"Total number of unused imports from all files: $unusedImportCount\n");
    }

    public function synchronousOutput()
    {
        $fileCount = 0;
        $unusedImportCount = 0;
        $unusedImportsByFile = [];

        foreach ($this->generator as $result) {
            $unusedImports = $result['unusedIdentifiers'];

            $fileCount++;
            $unusedImportCount += count($unusedImports);

            $unusedImportsByFile[$result['filename']] = $unusedImports;
        }

        return [
            'unusedImports' => $unusedImportsByFile,
            'totalUnusedFiles' => $fileCount,
            'totalUnusedIdentifiers' => $unusedImportCount,
        ];
    }
}