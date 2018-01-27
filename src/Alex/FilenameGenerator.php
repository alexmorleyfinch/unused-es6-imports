<?php

namespace Alex;


class FilenameGenerator
{
    private $fileRegex;
    private $recurseDirectories;

    public function __construct(string $fileRegex = '', bool $recurseDirectories = false)
    {
        $this->fileRegex = $fileRegex;
        $this->recurseDirectories = $recurseDirectories;
    }

    public function recurseFiles($path)
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
