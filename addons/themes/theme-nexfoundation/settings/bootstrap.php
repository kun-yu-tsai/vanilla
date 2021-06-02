<?php

function module_loader ($class_name) {
    if (!str_contains($class_name, 'Module')) {
        return;
    }
    $filename = dirname(__FILE__, 2).'/library/modules/class.'.strtolower($class_name). '.php';
    if (is_readable($filename)) {
        require($filename);
    }
}

function library_loader($class_name) {
    if (!preg_match('/([A-Z][a-z]+)$/', $class_name, $groups)){
        return;
    }
    $filename = dirname(__FILE__, 2).'/library/'.
                strtolower($groups[1]).
                's/class.'.strtolower($class_name).'.php';
    if (is_readable($filename)) {
        require($filename);
    }
}

spl_autoload_register('module_loader');
spl_autoload_register('library_loader');

use Vanilla\Utility\ContainerUtils;
$container = \Gdn::getContainer();

$container
    ->rule(TemplateLoader::class)
    ->setClass(TemplateLoader::class)
;
$container
    ->rule(TemplateHelper::class)
    ->setClass(TemplateHelper::class)
    ->setShared(true)
;

$config = $container->get('Config');
if ($config->get('Vanilla.Comments.AutoOffset', false)) {
    $config->set('Vanilla.Comments.AutoOffset', false);
}
?>
