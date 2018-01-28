<?php

namespace Almofi\UnusedEs6Imports\App;

class UnusedImportRunner
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var UnusedImportGenerator
     */
    private $unusedImportGenerator;

    /**
     * Allows us to run the UnusedImportGenerator as a stream of data or synchronously
     *
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
        $this->unusedImportGenerator = new UnusedImportGenerator();
    }

    /**
     * @param $stream // a resource like STDOUT or fopen('myfile')
     * @param callable $toString
     */
    public function streamOutput($stream, callable $toString)
    {
        $generator = $this->unusedImportGenerator->generateUnusedImportIdentifiers($this->rootDir);

        $fileCount = 0;
        $unusedImportCount = 0;

        foreach ($generator as $result) {
            $fileCount++;
            $unusedImportCount += count($result['unusedIdentifiers']);

            fwrite($stream, $toString($result) . "\n");
        }

        fwrite($stream, "\nTotal number of files with unused imports: $fileCount\n");
        fwrite($stream,"Total number of unused imports from all files: $unusedImportCount\n");
    }

    public function synchronousOutput()
    {
        $generator = $this->unusedImportGenerator->generateUnusedImportIdentifiers($this->rootDir);

        $fileCount = 0;
        $unusedImportCount = 0;
        $unusedImportsByFile = [];

        foreach ($generator as $result) {
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
