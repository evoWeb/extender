<?php

declare(strict_types=1);

/*
 * This file is part of the "extender" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Evoweb\Extender\Utility;

/**
 * This file is friendly lent from the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class ClassParser
{
    /**
     * @var int
     */
    public const STATE_CLASS_HEAD = 100001;

    /**
     * @var int
     */
    public const STATE_FUNCTION_HEAD = 100002;

    private array $classes = [];

    private array $extends = [];

    private array $implements = [];

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getFirstClass(): array
    {
        return array_shift($this->classes);
    }

    public function getClassesImplementing(string $interface): array
    {
        $implementers = [];
        if (isset($this->implements[$interface])) {
            foreach ($this->implements[$interface] as $name) {
                $implementers[$name] = $this->classes[$name];
            }
        }
        return $implementers;
    }

    public function getClassesExtending(string $className): array
    {
        $extenders = [];
        if (isset($this->extends[$className])) {
            foreach ($this->extends[$className] as $name) {
                $extenders[$name] = $this->classes[$name];
            }
        }
        return $extenders;
    }

    public function parse(string $content): void
    {
        $tokens = token_get_all($content);
        $classes = [];
        $classCount = 0;

        $depth = 0;
        $mod = [];
        $doc = null;
        $state = null;
        $inFunction = false;
        $functionName = '';
        $lastLine = 0;
        foreach ($tokens as $token) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_DOC_COMMENT:
                        $doc = $token[1];
                        break;

                    case T_PUBLIC:
                    case T_PRIVATE:
                    case T_ABSTRACT:
                    case T_PROTECTED:
                        $mod[] = $token[1];
                        break;

                    case T_CLASS:
                    case T_FUNCTION:
                        $state = $token[0];
                        break;

                    case T_EXTENDS:
                    case T_IMPLEMENTS:
                        switch ($state) {
                            case self::STATE_CLASS_HEAD:
                            case T_EXTENDS:
                                $state = $token[0];
                                break;
                        }
                        break;

                    case T_CLOSE_TAG:
                        $classes[$depth]['eol'] = $token[2];
                        break;

                    case T_STRING:
                        switch ($state) {
                            case T_CLASS:
                                $state = self::STATE_CLASS_HEAD;
                                $classes[] = [
                                    'name' => $token[1],
                                    'modifiers' => $mod,
                                    'doc' => $doc,
                                    'start' => $token[2]
                                ];
                                break;
                            case T_FUNCTION:
                                $state = self::STATE_FUNCTION_HEAD;
                                $classCount = count($classes);
                                if ($depth > 0 && $classCount) {
                                    $inFunction = true;
                                    $functionName = $token[1];
                                    $classes[$classCount - 1]['functions'][$token[1]] = [
                                        'modifiers' => $mod,
                                        'doc' => $doc,
                                        'start' => $token[2]
                                    ];
                                }
                                break;
                            case T_IMPLEMENTS:
                            case T_EXTENDS:
                                $classCount = count($classes);
                                $classes[$classCount - 1][
                                    $state == T_IMPLEMENTS ? 'implements' : 'extends'
                                ][] = $token[1];
                                break;
                        }
                        break;
                }
                $lastLine = $token[2];
            } else {
                switch ($token) {
                    case '{':
                        $depth++;
                        break;

                    case '}':
                        if ($inFunction) {
                            $classes[$classCount - 1]['functions'][$functionName]['end'] = $lastLine;
                            $inFunction = false;
                        }
                        $depth--;
                        break;
                }

                switch ($token) {
                    case '{':
                    case '}':
                    case ';':
                        $state = 0;
                        $doc = null;
                        $mod = [];
                        break;
                }
            }
        }

        foreach ($classes as $class) {
            $this->classes[$class['name']] = $class;

            if (!empty($class['implements'])) {
                foreach ($class['implements'] as $name) {
                    $this->implements[$name][] = $class['name'];
                }
            }

            if (!empty($class['extends'])) {
                foreach ($class['extends'] as $name) {
                    $this->extends[$name][] = $class['name'];
                }
            }
        }
    }
}
