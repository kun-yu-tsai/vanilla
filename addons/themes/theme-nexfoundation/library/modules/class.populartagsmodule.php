<?php
class PopularTagsModule extends Gdn_Module {
    public function assetTarget() {
        return 'Panel';
    }

    public function getTags() {
        return  Gdn::sql()
            ->where('t.CountDiscussions >', 0, false)
            ->where('t.Type', '')
            ->cache($tagCacheKey, 'get', [Gdn_Cache::FEATURE_EXPIRY => 120])
            ->select('t.*')
            ->from('Tag t')
            ->where('t.Type', '')
            ->orderBy('t.CountDiscussions', 'desc')
            ->limit(25)
            ->get()
            ->resultArray();
        ;
    }

    public function toString() {
        $string = '';
        ob_start();
        ?>
        <div class="Box Tags">
            <?php echo panelHeading(t('Popular Tags')); ?>
            <ul class="TagCloud">
                <?php foreach ($this->getTags() as $tag) :?>
                    <?php if ($tag['Name'] != '') :?>
                        <li class="TagCloud-Item"><?php
                            echo anchor(
                                // modify the Vanilla codebase
                                htmlspecialchars(tagFullName($tag)),
                                tagUrl($tag, '', '/'),
                                ['class' => 'Tag_'.str_replace(' ', '_', $tag['Name'])]
                            );
                            ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        $string = ob_get_clean();
        return $string;
    }
}
?>
