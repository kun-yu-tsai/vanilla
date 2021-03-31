<?php

use Garden\Web\Data;
use Vanilla\Web\JsInterpop\ReduxAction;

class TagLoader implements TemplateLoader {

    /**
     *
     * @param PageControllerWithRedux
     */
    public function load($sender) {
        $container = Gdn::getContainer();
        $tags = array_map(function ($tag) {
            return $tag['Name'];
        }, $this->_getData());
        $sender->addReduxAction(new ReduxAction(
            "@@nex/GET_TAGS_DONE",
            Data::box([
                'tags' => $tags,
            ]),
            []
        ));
    }

    protected function _getData() {
        $tagQuery = Gdn::sql();
        $tagCacheKey = 'TagModule-Global';
        $tagQuery->where('t.CountDiscussions >', 0, false)
            ->where('t.Type', '') // Only show user generated tags
            ->cache($tagCacheKey, 'get', [Gdn_Cache::FEATURE_EXPIRY => 120]);

        if ($this->CategorySearch) {
            $tagQuery->where('t.CategoryID', '-1');
        }
        $result = $tagQuery
                ->select('t.*')
                ->from('Tag t')
                ->where('t.Type', '') // Only show user generated tags
                ->orderBy('t.CountDiscussions', 'desc')
                ->limit(25)
                ->get();
        $result->datasetType(DATASET_TYPE_ARRAY);
        return $result->result();
    }
}
?>
