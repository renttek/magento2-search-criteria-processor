<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.2.1|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
                   '@PSR12:risky' => true,
                   '@PSR12' => true,
                   'blank_line_after_namespace' => false,
               ])
    ->setFinder(PhpCsFixer\Finder::create()
                    ->exclude('vendor')
                    ->in(__DIR__)
    )
    ;
