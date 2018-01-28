<?php

namespace Almofi\UnusedEs6Imports\Utils;

class FilenameGenerator
{
    /**
     * @var string
     */
    private $fileRegex;

    /**
     * @var bool
     */
    private $recurseDirectories;

    /**
     * @param string $fileRegex
     * @param bool $recurseDirectories
     */
    public function __construct(string $fileRegex = '', bool $recurseDirectories = false)
    {
        $this->fileRegex = $fileRegex;
        $this->recurseDirectories = $recurseDirectories;
    }

    /**
     * @param string $path
     * @return \Generator
     */
    public function recurseFiles(string $path): \Generator
    {
        foreach (glob("$path/*") as $idx => $filename) {
            if (is_dir($filename) && $filename != '..' && $this->recurseDirectories) {
                yield from $this->recurseFiles($filename);
                continue;
            }

            if ($this->fileRegex) {
                $matchCount = preg_match($this->fileRegex, $filename);

                if (!$matchCount) {
                    if ($matchCount === false) {
                        trigger_error('Regex error');
                    }
                    continue;
                }
            }

            yield $filename;
        }
    }
}
