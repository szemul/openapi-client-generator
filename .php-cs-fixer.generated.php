<?php

/**
 * CONFIG
 */
$dirs = [
    'src/',
    'test/',
];

$excludePaths = [];
$excludeDirs  = [];
$rules        = require_once __DIR__ . '/.php-cs-rules.php';

/**
 * CREATE FINDER
 */
$finder = PhpCsFixer\Finder::create()->in($dirs);

foreach ($excludeDirs as $dir) {
    $finder->exclude($dir);
}
foreach ($excludePaths as $path) {
    $finder->notPath($path);
}

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder)
    ->setRiskyAllowed(true);
