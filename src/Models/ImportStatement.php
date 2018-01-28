<?php

namespace Almofi\UnusedEs6Imports\Models;

class ImportStatement {
    /**
     * @var string
     */
    private $defaultImport;

    /**
     * @var string
     */
    private $dependancyName;

    /**
     * @var string
     */
    private $allAlias;

    /**
     * @var array
     */
    private $namedImports = [];


    public function setDefaultImport(string $import) {
        $this->defaultImport = $import;
    }

    public function setAllAlias(string $alias) {
        $this->allAlias = $alias;
    }

    public function setDependancyName(string $dependency) {
        $this->dependancyName = $dependency;
    }

    public function addNamedImport(string $import, string $alias = null) {
        $this->namedImports[] = ['import' => $import, 'alias' => $alias];
    }

    public function getNamedImports() {
        return array_merge(
            array_map(function($item) {
                return $item['alias'] ?: $item['import'];
            }, $this->namedImports),
            array_filter([$this->defaultImport, $this->allAlias])
        );
    }
}
