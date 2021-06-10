<?php
if (!function_exists('WriteDiscussion')) :
    /**
     *
     *
     * @param $discussion
     * @param $sender
     * @param $session
     */
    function writeDiscussion($discussion, $sender, $session) {
        $cssClass = cssClass($discussion);
        $discussionUrl = $discussion->Url;
        $category = CategoryModel::categories($discussion->CategoryID);
        /** @var Vanilla\Formatting\DateTimeFormatter */
        $dateTimeFormatter = Gdn::getContainer()->get(\Vanilla\Formatting\DateTimeFormatter::class);


        if ($session->UserID && $sender->data('ShowLastComment', true)) {
            $discussionUrl .= '#latest';
        }
        $sender->EventArguments['DiscussionUrl'] = &$discussionUrl;
        $sender->EventArguments['Discussion'] = &$discussion;
        $sender->EventArguments['CssClass'] = &$cssClass;

        $first = userBuilder($discussion, 'First');
        $last = userBuilder($discussion, 'Last');
        $sender->EventArguments['FirstUser'] = &$first;
        $sender->EventArguments['LastUser'] = &$last;

        $sender->fireEvent('BeforeDiscussionName');

        $discussionName = $discussion->Name;
        $sender->EventArguments['DiscussionName'] = &$discussionName;

        static $firstDiscussion = true;
        if (!$firstDiscussion) {
            $sender->fireEvent('BetweenDiscussion');
        } else {
            $firstDiscussion = false;
        }

        $discussion->CountPages = ceil($discussion->CountComments / $sender->CountCommentsPerPage);
        ?>
        <li id="Discussion_<?php echo $discussion->DiscussionID; ?>" class="<?php echo $cssClass; ?>" data-meta='<?php echo $discussion->DataAttribute; ?>'>
            <?php
            if (!property_exists($sender, 'CanEditDiscussions')) {
                $sender->CanEditDiscussions = val('PermsDiscussionsEdit', CategoryModel::categories($discussion->CategoryID)) && c('Vanilla.AdminCheckboxes.Use');
            }
            $sender->fireEvent('BeforeDiscussionContent');
            ?>
            <span class="Options">
                <?php
                echo optionsList($discussion);
                ?>
            </span>

            <div class="ItemContent Discussion">
                <div class="tag" id="tag_<?php echo $discussion->DiscussionID; ?>">
                </div>
                <div class="Title" role="heading" aria-level="3">
                    <?php
                    echo adminCheck($discussion, ['', ' ']).anchor($discussionName, $discussionUrl);
                    $sender->fireEvent('AfterDiscussionTitle');
                    ?>
                </div>
                <?php
                writeDiscussionExcerpt($discussion);
                ?>
                <br> <!-- TODO: replace this padding by utilising CSS -->
                <div class="Meta Meta-Discussion">
                    <?php
                    writeTags($discussion);

                    if ($sender->data('_ShowCategoryLink', true) && $category && c('Vanilla.Categories.Use') &&
                        CategoryModel::checkPermission($category, 'Vanilla.Discussions.View')) {
                        echo wrap(
                            anchor(htmlspecialchars($discussion->Category),
                        categoryUrl($discussion->CategoryUrlCode)/*, $accessibleAttributes */),
                            'span',
                            ['class' => 'MItem Category '.$category['CssClass']]
                        );
                    }
                    ?>
                    <?php Gdn::getContainer()->get(ArticleHelper::class)->writeBookmarkAnchor($discussion); ?>

                    <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.55914 20C7.29393 20 7.03957 19.8946 6.85204 19.7071C6.6645 19.5196 6.55914 19.2652 6.55914 19C6.55914 17.3431 5.216 16 3.55914 16H2.55914C2.02871 16 1.52 15.7893 1.14493 15.4142C0.769857 15.0391 0.559143 14.5304 0.559143 14V2C0.559143 1.46957 0.769857 0.960859 1.14493 0.585786C1.52 0.210714 2.02871 0 2.55914 0H18.5591C19.0896 0 19.5983 0.210714 19.9734 0.585786C20.3484 0.960859 20.5591 1.46957 20.5591 2V14C20.5591 14.5304 20.3484 15.0391 19.9734 15.4142C19.5983 15.7893 19.0896 16 18.5591 16H17.6988C14.3444 16 11.1279 17.3349 8.75914 19.71C8.55914 19.9 8.30914 20 8.05914 20H7.55914ZM14.5591 9C15.1114 9 15.5591 8.55229 15.5591 8C15.5591 7.44772 15.1114 7 14.5591 7C14.0069 7 13.5591 7.44772 13.5591 8C13.5591 8.55229 14.0069 9 14.5591 9ZM10.5591 9C11.1114 9 11.5591 8.55229 11.5591 8C11.5591 7.44772 11.1114 7 10.5591 7C10.0069 7 9.55914 7.44772 9.55914 8C9.55914 8.55229 10.0069 9 10.5591 9ZM6.55914 9C7.11143 9 7.55914 8.55229 7.55914 8C7.55914 7.44772 7.11143 7 6.55914 7C6.00686 7 5.55914 7.44772 5.55914 8C5.55914 8.55229 6.00686 9 6.55914 9Z" fill="#FF3559"/>
                    </svg>

                    <span class="MItem MCount CommentCount"><?php
                        printf(pluralTranslate($discussion->CountComments,
                            '%s comment html', '%s comments html', t('%s comment'), t('%s comments')),
                            bigPlural($discussion->CountComments, '%s comment'));
                    ?></span>
                    <span class="MItem MCount DiscussionScore Hidden"><?php
                        $score = $discussion->Score;
                        if ($score == '') $score = 0;
                        printf(
                            plural($score,
                            '%s point', '%s points',
                            bigPlural($score, '%s point'))
                        );
                    ?></span>
                    <?php
                    echo newComments($discussion);
                    $layout = c('Vanilla.Categories.Layout');

                    $sender->fireEvent('AfterCountMeta');

                    $discussionName = is_array($discussion) ? $discussion['Name'] : $discussion->Name;

                    if ($discussion->LastCommentID != '' && $sender->data('ShowLastComment', true)) {
                        echo ' <span class="MItem LastCommentBy">'.sprintf(t('Most recent by %1$s'), userAnchor($last)).'</span> ';
                        echo ' <span class="MItem LastCommentDate">'.Gdn_Format::date($discussion->LastDate, "html").'</span>';
                        $userName = $last->Name;

                        if ($layout !== "mixed") {
                            $template = t('Most recent comment on date %s, in discussion "%s", by user "%s"');
                            $accessibleVars = [$dateTimeFormatter->formatDate($discussion->LastDate, false), $discussionName, $userName];
                        } else {
                            $template = t('Category: "%s"');
                            $accessibleVars = [$discussion->Category];
                        }

                    } else {
                        ?><div class="break"></div><?php
                        Gdn::getContainer()->get(ArticleHelper::class)->writeUserAnchor($first);
                        echo '<span class="MItem LastCommentDate">'.
                            $dateTimeFormatter->formatDate($discussion->FirstDate, true, " %Y/%m/%d").
                            "</span>"
                        ;
                        $template = t('User "%s" started discussion "%s" on date %s');
                        $userName = $first->Name;
                        $accessibleVars = [$userName, $discussionName, $dateTimeFormatter->formatDate($discussion->FirstDate, false)];
                    }
                    $sender->fireEvent('DiscussionMeta');
                    ?>
                </div>
            </div>
            <?php $sender->fireEvent('AfterDiscussionContent'); ?>
        </li>
    <?php
    }
endif;
?>
