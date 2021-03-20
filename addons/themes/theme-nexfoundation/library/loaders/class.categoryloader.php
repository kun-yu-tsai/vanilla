<?php

use Garden\Web\Data;
use Vanilla\Web\JsInterpop\ReduxAction;

class CategoryLoader implements TemplateLoader {

    /**
     * @param PageControllerWithRedux $sender
     */
    public function load($sender) {
        $model = CategoryModel::instance();
        $names = array_values(
            array_map(function ($category) {
                return $category['Name'];
            }, array_filter($model->categories(), function ($category) {
                return $category['CategoryID'] > 0;
            }))
        );
        $sender->addReduxAction(new ReduxAction(
            "@@nex/GET_CATEGORY_DONE",
            Data::box([
                'names' => $names
            ]),
            []
        ));
    }

}

?>