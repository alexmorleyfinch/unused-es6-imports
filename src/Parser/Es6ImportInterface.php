<?php

namespace Almofi\UnusedEs6Imports\Parser;

interface Es6ImportInterface
{
    /**
     * @param string $import
     */
    public function setDefaultImport(string $import);

    /**
     * @param string $alias
     */
    public function setAllAlias(string $alias);

    /**
     * @param string $dependency
     */
    public function setDependancyName(string $dependency);

    /**
     * @param string $import
     * @param string|null $alias
     */
    public function addNamedImport(string $import, string $alias = null);
}
