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

        // tagged page needs to show panel #77
        if ($sender->RequestMethod == "tagged") {
            $sender->addModule('CategoriesModule');
            $sender->addModule('PopularTagsModule');
        }
    }

    public function categoriescontroller_BeforeBuildPager_handler($sender) {
        $sender->setData('ShowLastComment', false);
    }

    public function discussioncontroller_BeforeDiscussionRender_handler($sender) {
        $sender->addModule('PopularTagsModule');
    }


    /**
     * @link https://github.com/nexfoundation/vanilla/issues/68
     *
     * We forcibly set category to null in order to make all new post category-agnostic.
     */
    public function categoriescontroller_BeforeNewDiscussionButton_handler($sender) {
        $panel = $sender->getAsset('Panel');
        foreach ($panel->Items as $item) {
            if (is_a($item, NewDiscussionModule::class)) {
                $item->CategoryID = null;
            }
        }
    }

    /**
     * event from PostController before request newly created comment
     *
     */
    public function base_BeforeCommentRender_handler($sender) {
        require_once $sender->fetchViewLocation('override_functions', 'Discussion');
    }

    public function setup() {
        $this->structure();
    }

    public function structure() {}
}
?>
