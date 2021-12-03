<?php

$rules = require_once __DIR__ . '/.php-cs-rules.php';

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setRiskyAllowed(true);
