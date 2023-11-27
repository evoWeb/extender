<?php

$EM_CONF['extender'] = [
    'title' => 'Extbase Domain Model Extender',
    'description' => 'A services that enables adding properties and functions
    to classes by implementing the proxy pattern',
    'category' => 'misc',
    'author' => 'Sebastian Fischer',
    'author_email' => 'extender@evoweb.de',
    'author_company' => 'evoWeb',
    'state' => 'stable',
    'version' => '10.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.0.0-12.2.99',
        ],
    ],
];
