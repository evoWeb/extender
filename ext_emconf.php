<?php

$EM_CONF['extender'] = [
    'title' => 'Extbase Domain Model Extender',
    'description' => 'A services that enables adding properties and functions
    to classes by implementing the proxy pattern',
    'category' => 'misc',
    'author' => 'Sebastian Fischer',
    'author_email' => 'typo3@evoweb.de',
    'author_company' => 'evoweb',
    'state' => 'stable',
    'clearcacheonload' => 1,
    'priority' => 'bottom',
    'version' => '6.4.8',
    'constraints' => [
        'depends' => [
            'php' => '5.5.0-7.2.99',
            'typo3' => '6.2.10-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload-dev' => [
        'psr-4' => [
            'Evoweb\\Extender\\Tests\\' => 'Tests/',
        ],
    ],
];
