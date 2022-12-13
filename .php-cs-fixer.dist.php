<?php

$dirs = [
    'src/',
    'test/',
];

$excludePaths = [];
$excludeDirs  = [
    'test/Functional/data',
];

$rules = require_once __DIR__ . '/.php-cs-rules.php';

$finder = PhpCsFixer\Finder::create()->in($dirs);

foreach ($excludeDirs as $dir) {
    $finder->exclude($dir);
}
foreach ($excludePaths as $path) {
    $finder->notPath($path);
}

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true);
