<?php

use Garden\Web\Data;
use Vanilla\Web\JsInterpop\ReduxAction;

class MetaLoader implements TemplateLoader {


    /**
     *
     * @param PageControllerWithRedux
     */
    public function load($sender) {
        $currentTheme = \Gdn::getContainer()
            ->get(\Vanilla\AddonManager::class)
            ->getTheme()
        ;
        $sender->addReduxAction(new ReduxAction(
            "@@nex/GET_META_DONE",
            Data::box([
                'currentThemePath' => $currentTheme->getSubdir(),
            ]),
            []
        ));
    }
}

?>
