<?php

declare(strict_types=1);

/*
 * This file is developed by evoWeb.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Parser;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\UseItem;
use PhpParser\Node;

class FileSegments implements \JsonSerializable
{
    protected string $filePath = '';

    protected bool $baseClass = false;

    protected string $code = '';

    /**
     * @var Node[]
     */
    protected array $statements = [];

    protected ?Name $namespace = null;

    /**
     * @var UseItem[]
     */
    protected array $uses = [];

    protected ?Class_ $class = null;

    /**
     * @var TraitUse[]
     */
    protected array $traits = [];

    /**
     * @var ClassConst[]
     */
    protected array $classConsts = [];

    /**
     * @var Property[]
     */
    protected array $properties = [];

    protected ?ClassMethod $constructor = null;

    protected ?ClassMethod $initializeObject = null;

    /**
     * @var ClassMethod[]
     */
    protected array $methods = [];

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function isBaseClass(): bool
    {
        return $this->baseClass;
    }

    public function setBaseClass(bool $baseClass): void
    {
        $this->baseClass = $baseClass;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return Node[]
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    /**
     * @param Node[] $statements
     */
    public function setStatements(array $statements): void
    {
        $this->statements = $statements;
    }

    public function getNamespace(): ?Name
    {
        return $this->namespace;
    }

    public function setNamespace(?Name $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return UseItem[]
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @param UseItem[] $uses
     */
    public function setUses(array $uses): void
    {
        $this->uses = $uses;
    }

    public function addUse(UseItem $use): void
    {
        $this->uses[] = $use;
    }

    public function getClass(): ?Class_
    {
        return $this->class;
    }

    public function setClass(?Class_ $class): void
    {
        $this->class = $class;
    }

    /**
     * @return TraitUse[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param TraitUse[] $traits
     */
    public function setTraits(array $traits): void
    {
        $this->traits = $traits;
    }

    public function addTrait(TraitUse $traitUse): void
    {
        $this->traits[] = $traitUse;
    }

    /**
     * @return ClassConst[]
     */
    public function getClassConsts(): array
    {
        return $this->classConsts;
    }

    /**
     * @param ClassConst[] $classConst
     */
    public function setClassConsts(array $classConst): void
    {
        $this->classConsts = $classConst;
    }

    public function addClassConst(ClassConst $classConst): void
    {
        $this->classConsts[] = $classConst;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param Property[] $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function addProperty(Property $property): void
    {
        $this->properties[] = $property;
    }

    public function getConstructor(): ?ClassMethod
    {
        return $this->constructor;
    }

    public function setConstructor(?ClassMethod $constructor): void
    {
        $this->constructor = $constructor;
    }

    public function getInitializeObject(): ?ClassMethod
    {
        return $this->initializeObject;
    }

    public function setInitializeObject(?ClassMethod $initializeObject): void
    {
        $this->initializeObject = $initializeObject;
    }

    /**
     * @return ClassMethod[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param ClassMethod[] $methods
     */
    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    public function addMethod(ClassMethod $classMethod): void
    {
        $this->methods[] = $classMethod;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'filePath' => $this->filePath,
            'baseClass' => $this->baseClass,
            'code' => $this->code,
            'statements' => $this->statements,
            'namespace' => $this->namespace,
            'uses' => $this->uses,
            'class' => $this->class,
            'traits' => $this->traits,
            'properties' => $this->properties,
            'constructor' => $this->constructor,
            'methods' => $this->methods,
        ];
    }
}
