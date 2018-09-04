<?php

require_once __DIR__ . '/vendor/autoload.php';


$baseNamespace = [
];
$runner = new \Starkerxp\CodeMigrator\Runner('./project/');
$runner->addAdapter(new \Starkerxp\CodeMigrator\Adapter\ReplaceContent(["\r\n"], "\n"));
$runner->addAdapter(new \Starkerxp\CodeMigrator\Adapter\ObjectExtractor($baseNamespace));
// Rename Class / Interface with reserverd name / and replace in files
$runner->addAdapter(new \Starkerxp\CodeMigrator\Adapter\ChangeFileExtension('.class.php', '.php'));
$runner->addAdapter(new \Starkerxp\CodeMigrator\Adapter\ValidNamespace($baseNamespace));
$runner->addAdapter(new \Hektor\CodeMigrator\Adapter\ConvertRequireToUseForHektor());
$runner->addAdapter(new \Starkerxp\CodeMigrator\Adapter\RemoveComment());
// Legacy code migrate to class base on filename
$runner->run();
