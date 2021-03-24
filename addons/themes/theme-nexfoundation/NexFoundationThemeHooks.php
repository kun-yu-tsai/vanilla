<?php
require_once("settings/bootstrap.php");

class NexFoundationThemeHooks extends Gdn_Plugin {

    /**
     *
     * @param Gdn_Controller $sender The object calling this method.
     */
    public function base_render_before($sender) {
        // Fetch the currently enabled locale (en by default)
        $adModule = new AdModule();
        $sender->addModule($adModule);
        $this->run_loaders($sender);
    }

    /**
     * @param PageControllerWithRedux $sender
     */
    public function run_loaders($sender) {
        $container = Gdn::getContainer();
        $loaders = [TagLoader::class, CategoryLoader::class, MetaLoader::class];
        foreach ($loaders as $loader) {
            $loaderInstance = $container->get($loader);
            $loaderInstance->load($sender);
        }
    }

    /**
     * This is the handler to catch event fire from:
     *
     *  library/Vanilla/Controllers/SearchRootController.php
     *
     * @param Vanilla\Web\Page $sender
     */
    public function beforeSearchRootRender_handler($sender) {
        $this->run_loaders($sender);
    }

    /**
     * @param DiscussionsController $sender
     */
    public function discussionscontroller_BeforeBuildPager_handler($sender) {
        $sender->setData('ShowLastComment', false);
    }

    public function setup() {
        $this->structure();
    }

    public function structure() {}
}
?>
