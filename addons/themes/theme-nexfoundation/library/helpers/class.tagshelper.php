<?php

class TagsHelper {

    public function writeMetaTags() {
        $module = Gdn::getContainer()->get(TagModule::class);
        ?>
        <div class="Box Tags">
            <ul class="TagCloud">
                <?php foreach ($module->getTags() as $tag) : ?>
                    <?php if ($tag['Name'] != '') : ?>
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
    }
}
?>
