<?php
require_once("settings/bootstrap.php");

use Garden\Web\Data;
use Vanilla\Web\JsInterpop\ReduxActionPreloadTrait;
use Vanilla\Web\JsInterpop\ReduxAction;

class NexFoundationThemeHooks extends Gdn_Plugin {

    use ReduxActionPreloadTrait;

    /**
     *
     * @param Gdn_Controller $sender The object calling this method.
     */
    public function base_render_before($sender) {
        // Fetch the currently enabled locale (en by default)
        $adModule = new AdModule();
        $sender->addModule($adModule);
        $this->run_loaders();
    }

    public function run_loaders() {
        $container = Gdn::getContainer();
        $loaders = [TagLoader::class, CategoryLoader::class];
        foreach ($loaders as $loader) {
            $container->get($loader);
        }
    }

    public function base_Register_handler($sender) {}
}
?>
