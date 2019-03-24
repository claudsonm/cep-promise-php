<?php

$header = <<<'EOF'
This file is part of PHP CS Fixer.

(c) Fabien Potencier <fabien@symfony.com>
    Dariusz Rumiński <dariusz.ruminski@gmail.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->notPath('bootstrap/cache')
    ->notPath('node_modules')
    ->notPath('storage')
    ->notPath('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->notName('_ide_helper.php')
    ->notName('_ide_helper_models.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

$config = PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        '@PSR2' => true,
        '@PSR1' => true,
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_useless_return' => true,
        'not_operator_with_successor_space' => true,
        'ordered_class_elements' => ['sortAlgorithm' => 'alpha'],
        'ordered_imports' => ['imports_order' => ['const', 'class', 'function']],
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_order' => true,
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
    ])
    ->setFinder($finder)
;

// special handling of fabbot.io service if it's using too old PHP CS Fixer version
if (false !== getenv('FABBOT_IO')) {
    try {
        PhpCsFixer\FixerFactory::create()
            ->registerBuiltInFixers()
            ->registerCustomFixers($config->getCustomFixers())
            ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()));
    } catch (PhpCsFixer\ConfigurationException\InvalidConfigurationException $e) {
        $config->setRules([]);
    } catch (UnexpectedValueException $e) {
        $config->setRules([]);
    } catch (InvalidArgumentException $e) {
        $config->setRules([]);
    }
}

return $config;

/*
This document has been generated with
https://mlocati.github.io/php-cs-fixer-configurator/?version=2.13#configurator
you can change this configuration by importing this YAML code:

version: 2.13.0
expandSets: false
fixerSets:
  - '@PSR2'
  - '@PSR1'
  - '@Symfony'
fixers:
  array_syntax:
    syntax: short
  heredoc_to_nowdoc: true
  linebreak_after_opening_tag: true
  method_argument_space:
    on_multiline: ensure_fully_multiline
  no_useless_return: true
  not_operator_with_successor_space: true
  ordered_class_elements:
    sortAlgorithm: alpha
  ordered_imports:
    imports_order:
      - const
      - class
      - function
  phpdoc_add_missing_param_annotation:
    only_untyped: false
  phpdoc_order: true
  phpdoc_types_order:
    null_adjustment: always_last

*/