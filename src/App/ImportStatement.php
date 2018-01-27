<?php

namespace Almofi\UnusedEs6Imports\App;

class ImportStatement {
    private $defaultImport; // you can only have one default import per statement
    private $namedImports = []; // array of imports that were destructured {foo, blah}
    private $dependancyName; // the "components/button" or "react-redux" part
    private $allAlias;

    public function setDefaultImport($import) {
        $this->defaultImport = $import;
    }

    public function setAllAlias($alias) {
        $this->allAlias = $alias;
    }

    public function setDependancyName($dependency) {
        $this->dependancyName = $dependency;
    }

    public function addNamedImport($import, $alias = null) {
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
