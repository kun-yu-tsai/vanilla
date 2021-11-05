<?php
if (!defined('APPLICATION')) exit();
use Vanilla\Utility\HtmlUtils;

if (!function_exists('AdminCheck')) {
    /**
     *
     *
     * @param null $discussion
     * @param bool|FALSE $wrap
     * @return string
     */
    function adminCheck($discussion = null, $wrap = FALSE) {
        static $useAdminChecks = NULL;
        if ($useAdminChecks === null) {
            $useAdminChecks = c('Vanilla.AdminCheckboxes.Use') && Gdn::session()->checkPermission('Garden.Moderation.Manage');
        }
        if (!$useAdminChecks) {
            return '';
        }

        static $canEdits = [], $checked = NULL;
        $result = '';

        if ($discussion) {
            if (!isset($canEdits[$discussion->CategoryID])) {
                $canEdits[$discussion->CategoryID] = val('PermsDiscussionsEdit', CategoryModel::categories($discussion->CategoryID));
            }

            if ($canEdits[$discussion->CategoryID]) {
                // Grab the list of currently checked discussions.
                if ($checked === null) {
                    $checked = (array)Gdn::session()->getAttribute('CheckedDiscussions', []);

                    if (!is_array($checked)) {
                        $checked = [];
                    }
                }

                if (in_array($discussion->DiscussionID, $checked))
                    $itemSelected = ' checked="checked"';
                else
                    $itemSelected = '';

                $result = '<span class="AdminCheck"><input type="checkbox" name="DiscussionID[]" aria-label="' . t('Select Discussion') . '" value="' . $discussion->DiscussionID . '" $itemSelected /></span>';
            }
        } else {
            $result = '<span class="AdminCheck"><input type="checkbox" aria-label="' . t('Select Discussion') . '" name="Toggle" /></span>';
        }

        if ($wrap) {
            $result = $wrap[0].$result.$wrap[1];
        }

        return $result;
    }
}

if (!function_exists('BookmarkButton')) {
    /**
     *
     *
     * @param $discussion
     * @return string
     */
    function bookmarkButton($discussion) {
        if (!Gdn::session()->isValid()) {
            return '';
        }

        // Bookmark link
        $isBookmarked = $discussion->Bookmarked == '1';
        $title = t($isBookmarked ? 'Unbookmark' : 'Bookmark');
        $accessibleLabel= HtmlUtils::accessibleLabel('%s for discussion: "%s"', [t($isBookmarked? 'Unbookmark' : 'Bookmark'), is_array($discussion) ? $discussion["Name"] : $discussion->Name]);

        return anchor(
            $title,
            '/discussion/bookmark/'.$discussion->DiscussionID.'/'.Gdn::session()->transientKey(),
            'Hijack Bookmark'.($discussion->Bookmarked == '1' ? ' Bookmarked' : ''),
            ['title' => $title, 'aria-label' => $accessibleLabel]
        );
    }
}

if (!function_exists('CategoryLink')) :
    /**
     *
     *
     * @param $discussion
     * @param string $prefix
     * @return string
     */
    function categoryLink($discussion, $prefix = ' ') {
        $category = CategoryModel::categories(val('CategoryID', $discussion));
        if ($category) {
            $name = is_array($category) ? $category["Name"] : $category->Name;
            $accessibleLabel= HtmlUtils::accessibleLabel('Category: "%s"', [$name]);
            return wrap(
        $prefix.anchor(htmlspecialchars($name), $category['Url'], ["aria-label" => $accessibleLabel]),
                'span',
                ['class' => 'MItem Category']
            );
        }
    }

endif;

if (!function_exists('DiscussionHeading')) :
    /**
     *
     *
     * @return string
     */
    function discussionHeading() {
        return t('Discussion');
    }

endif;

if (!function_exists('ExcerptBody')):
    /**
     * Excerpt partial content of discussion.
     * @since 2.1
     * @param DataSet $discussion discussion.
     * @return string Excerpt body.
     */
    function excerptBody($discussion) {

        $dom = new DOMDocument();
        $html = Gdn_Format::to($discussion->Body, $discussion->Format);
        $dom->loadHTML($html);
        $body = $dom->getElementsByTagName('body');
        if ($body->length <= 0) {
            return "";
        }
        $body = $body->item(0);
        return utf8_decode($body->textContent);
    }
endif;

if (!function_exists('WriteDiscussionExcerpt')):
    /**
     *
     *
     * @param $discussion
     */
    function writeDiscussionExcerpt($discussion) {
        $discussionUrl = $discussion->Url;
    ?>
        <div class="discussion-content">
            <div class="excerpt">
            <?php
                echo excerptBody($discussion);
                // TODO: bind discussion url to this excerpt.
                // echo adminCheck($discussion, ['', ' ']).anchor(excerptBody($discussion), $discussionUrl);
            ?>
            </div>
        </div>
    <?php
    }
endif;

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

        $discussionUrl = substr($discussionUrl, 0, strrpos($discussionUrl, "/"));

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
      echo bookmarkButton($discussion);
      ?>
   </span>

            <div class="ItemContent Discussion">
                <h1>Good</h>
                <div class="tag"  aria-level="3" id="tag_<?php echo $discussion->DiscussionID; ?>">
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
                <div class="Meta Meta-Discussion">
                    <?php
                    writeTags($discussion);

                    if ($sender->data('_ShowCategoryLink', true) && $category && c('Vanilla.Categories.Use') &&
                        CategoryModel::checkPermission($category, 'Vanilla.Discussions.View')) {
                        // Kun: Skip accessibleAttribute because we now allow access for all cateogories.
                        // $accessibleAttributes = ["tabindex" => "0", "aria-label" => HtmlUtils::accessibleLabel($template, $accessibleVars)];
                        // if ($layout === "mixed") { // The links to categories are duplicates and have no accessible value
                        //     $accessibleAttributes['tabindex'] = "-1";
                        //     $accessibleAttributes['aria-hidden'] = "true";
                        // }
                        echo wrap(
                            anchor(htmlspecialchars($discussion->Category),
                        categoryUrl($discussion->CategoryUrlCode)/*, $accessibleAttributes */),
                            'span',
                            ['class' => 'MItem Category '.$category['CssClass']]
                        );
                    }
                    ?>
                    <span class="MItem MCount ViewCount">
                    <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12.119 0.25C12.6369 0.25 13.1335 0.478273 13.4996 0.884602C13.8658 1.29093 14.0715 1.84203 14.0715 2.41667V19.75L7.23786 16.5L0.404221 19.75V2.41667C0.404221 1.84203 0.609927 1.29093 0.976086 0.884602C1.34224 0.478273 1.83886 0.25 2.35669 0.25H12.119ZM6.26163 4.58333V6.75H4.30916V8.91667H6.26163V11.0833H8.2141V8.91667H10.1666V6.75H8.2141V4.58333H6.26163Z" fill="#FF3559"/>
</svg>

                    <?php


                        echo($discussion->Bookmarked);
                        echo( t('bookmarked'));
                        ?></span>
         <span class="MItem MCount CommentCount">
         <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7.55914 20C7.29393 20 7.03957 19.8946 6.85204 19.7071C6.6645 19.5196 6.55914 19.2652 6.55914 19C6.55914 17.3431 5.216 16 3.55914 16H2.55914C2.02871 16 1.52 15.7893 1.14493 15.4142C0.769857 15.0391 0.559143 14.5304 0.559143 14V2C0.559143 1.46957 0.769857 0.960859 1.14493 0.585786C1.52 0.210714 2.02871 0 2.55914 0H18.5591C19.0896 0 19.5983 0.210714 19.9734 0.585786C20.3484 0.960859 20.5591 1.46957 20.5591 2V14C20.5591 14.5304 20.3484 15.0391 19.9734 15.4142C19.5983 15.7893 19.0896 16 18.5591 16H17.6988C14.3444 16 11.1279 17.3349 8.75914 19.71C8.55914 19.9 8.30914 20 8.05914 20H7.55914ZM14.5591 9C15.1114 9 15.5591 8.55229 15.5591 8C15.5591 7.44772 15.1114 7 14.5591 7C14.0069 7 13.5591 7.44772 13.5591 8C13.5591 8.55229 14.0069 9 14.5591 9ZM10.5591 9C11.1114 9 11.5591 8.55229 11.5591 8C11.5591 7.44772 11.1114 7 10.5591 7C10.0069 7 9.55914 7.44772 9.55914 8C9.55914 8.55229 10.0069 9 10.5591 9ZM6.55914 9C7.11143 9 7.55914 8.55229 7.55914 8C7.55914 7.44772 7.11143 7 6.55914 7C6.00686 7 5.55914 7.44772 5.55914 8C5.55914 8.55229 6.00686 9 6.55914 9Z" fill="#FF3559"/>
</svg>
             <?php
             printf(pluralTranslate($discussion->CountComments,
                 '%s comment html', '%s comments html', t('%s comment'), t('%s comments')),
                 bigPlural($discussion->CountComments, '%s comment'));
             ?></span>
         <span class="MItem MCount DiscussionScore Hidden"><?php
             $score = $discussion->Score;
             if ($score == '') $score = 0;
             printf(plural($score,
                 '%s point', '%s points',
                 bigPlural($score, '%s point')));
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
                        echo ' <span class="MItem LastCommentBy">'.sprintf(t('Started by %1$s'), userAnchor($first)).'</span> ';
                        echo ' <span class="MItem LastCommentDate">'.Gdn_Format::date($discussion->FirstDate, "html");
                        if ($source = val('Source', $discussion)) {
                            echo ' '.sprintf(t('via %s'), t($source.' Source', $source));
                        }
                        echo '</span> ';
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

if (!function_exists('WriteDiscussionSorter')) :
    /**
     *
     *
     * @param null $selected
     * @param null $options
     */
    function writeDiscussionSorter($selected = null, $options = null) {
        deprecated('writeDiscussionSorter', 'DiscussionSortFilterModule', 'March 2016');

        if ($selected === null) {
            $selected = Gdn::session()->getPreference('Discussions.SortField', 'DateLastComment');
        }
        $selected = stringBeginsWith($selected, 'd.', TRUE, true);

        $options = [
            'DateLastComment' => t('Sort by Last Comment', 'by Last Comment'),
            'DateInserted' => t('Sort by Start Date', 'by Start Date')
        ];

        ?>
        <span class="ToggleFlyout SelectFlyout">
        <?php
        if (isset($options[$selected])) {
            $text = $options[$selected];
        } else {
            $text = reset($options);
        }
        echo wrap($text.' '.sprite('', 'DropHandle'), 'span', ['class' => 'Selected']);
        ?>
            <div class="Flyout MenuItems">
                <ul>
                    <?php
                    foreach ($options as $sortField => $sortText) {
                        echo wrap(anchor($sortText, '#', ['class' => 'SortDiscussions', 'data-field' => $sortField]), 'li');
                    }
                    ?>
                </ul>
            </div>
         </span>
        <?php
    }
endif;

if (!function_exists('WriteMiniPager')) :
    /**
     *
     *
     * @param $discussion
     */
    function writeMiniPager($discussion) {
        if (!property_exists($discussion, 'CountPages')) {
            return;
        }

        if ($discussion->CountPages > 1) {
            echo '<span class="MiniPager">';
            if ($discussion->CountPages < 5) {
                for ($i = 0; $i < $discussion->CountPages; $i++) {
                    writePageLink($discussion, $i + 1);
                }
            } else {
                writePageLink($discussion, 1);
                writePageLink($discussion, 2);
                echo '<span class="Elipsis">...</span>';
                writePageLink($discussion, $discussion->CountPages - 1);
                writePageLink($discussion, $discussion->CountPages);
                // echo anchor('Go To Page', '#', 'GoToPageLink');
            }
            echo '</span>';
        }
    }
endif;

if (!function_exists('WritePageLink')):
    /**
     *
     *
     * @param $discussion
     * @param $pageNumber
     */
    function writePageLink($discussion, $pageNumber) {
        echo anchor($pageNumber, discussionUrl($discussion, $pageNumber));
    }
endif;

if (!function_exists('NewComments')) :
    /**
     *
     *
     * @param $discussion
     * @return string
     */
    function newComments($discussion) {
        if (!Gdn::session()->isValid())
            return '';

        if ($discussion->CountUnreadComments === TRUE) {
            $title = htmlspecialchars(t("You haven't read this yet."));

            return ' <strong class="HasNew JustNew NewCommentCount" title="'.$title.'">'.t('new discussion', 'new').'</strong>';
        } elseif ($discussion->CountUnreadComments > 0) {
            $title = htmlspecialchars(plural($discussion->CountUnreadComments, "%s new comment since you last read this.", "%s new comments since you last read this."));

            return ' <strong class="HasNew NewCommentCount" title="'.$title.'">'.plural($discussion->CountUnreadComments, '%s new', '%s new plural', bigPlural($discussion->CountUnreadComments, '%s new', '%s new plural')).'</strong>';
        }
        return '';
    }
endif;

if (!function_exists('tag')) :
    /**
     *
     *
     * @param $discussion
     * @param $column
     * @param $code
     * @param bool|false $cssClass
     * @return string|void
     */
    function tag($discussion, $column, $code, $cssClass = FALSE) {
        $discussion = (object)$discussion;

        if (is_numeric($discussion->$column) && !$discussion->$column)
            return '';
        if (!is_numeric($discussion->$column) && strcasecmp($discussion->$column, $code) != 0)
            return;

        if (!$cssClass)
            $cssClass = "Tag-$code";

        return ' <span class="Tag '.$cssClass.'" title="'.htmlspecialchars(t($code)).'">'.t($code).'</span> ';

    }
endif;

if (!function_exists('writeTags')) :
    /**
     *
     *
     * @param $discussion
     * @throws Exception
     */
    function writeTags($discussion) {
        Gdn::controller()->fireEvent('BeforeDiscussionMeta');

        echo tag($discussion, 'Announce', 'Announcement');
        echo tag($discussion, 'Closed', 'Closed');

        Gdn::controller()->fireEvent('AfterDiscussionLabels');
    }
endif;

if (!function_exists('writeFilterTabs')) :
    /**
     *
     *
     * @param $sender
     */
    function writeFilterTabs($sender) {
        $session = Gdn::session();
        $title = property_exists($sender, 'Category') ? val('Name', $sender->Category, '') : '';
        if ($title == '') {
            $title = t('All Discussions');
        }
        $bookmarked = t('My Bookmarks');
        $myDiscussions = t('My Discussions');
        $myDrafts = t('My Drafts');
        $countBookmarks = 0;
        $countDiscussions = 0;
        $countDrafts = 0;

        if ($session->isValid()) {
            $countBookmarks = $session->User->CountBookmarks;
            $countDiscussions = $session->User->CountDiscussions;
            $countDrafts = $session->User->CountDrafts;
        }

        if (c('Vanilla.Discussions.ShowCounts', true)) {
            $bookmarked .= countString($countBookmarks, '/discussions/UserBookmarkCount');
            $myDiscussions .= countString($countDiscussions);
            $myDrafts .= countString($countDrafts);
        }

        ?>
        <div class="Tabs DiscussionsTabs">
            <?php
            if (!property_exists($sender, 'CanEditDiscussions')) {
                $sender->CanEditDiscussions = $session->checkPermission('Vanilla.Discussions.Edit', true, 'Category', 'any') && c('Vanilla.AdminCheckboxes.Use');
            }
            if ($sender->CanEditDiscussions) {
                ?>
                <span class="Options"><span class="AdminCheck">
                    <input type="checkbox" aria-label="<?php echo t('Select Discussion') ?>" name="Toggle"/>
                </span></span>
            <?php } ?>
            <ul>
                <?php $sender->fireEvent('BeforeDiscussionTabs'); ?>
                <li<?php echo strtolower($sender->ControllerName) == 'discussionscontroller' && strtolower($sender->RequestMethod) == 'index' ? ' class="Active"' : ''; ?>><?php echo anchor(t('All Discussions'), 'discussions', 'TabLink'); ?></li>
                <?php $sender->fireEvent('AfterAllDiscussionsTab'); ?>

                <?php
                if (c('Vanilla.Categories.ShowTabs')) {
                    $cssClass = '';
                    if (strtolower($sender->ControllerName) == 'categoriescontroller' && strtolower($sender->RequestMethod) == 'all') {
                        $cssClass = 'Active';
                    }

                    echo " <li class=\"$cssClass\">".anchor(t('Categories'), '/categories/all', 'TabLink').'</li> ';
                }
                ?>
                <?php if ($countBookmarks > 0 || $sender->RequestMethod == 'bookmarked') { ?>
                    <li<?php echo $sender->RequestMethod == 'bookmarked' ? ' class="Active"' : ''; ?>><?php echo anchor($bookmarked, '/discussions/bookmarked', 'MyBookmarks TabLink'); ?></li>
                    <?php
                    $sender->fireEvent('AfterBookmarksTab');
                }
                if (($countDiscussions > 0 || $sender->RequestMethod == 'mine') && c('Vanilla.Discussions.ShowMineTab', true)) {
                    ?>
                    <li<?php echo $sender->RequestMethod == 'mine' ? ' class="Active"' : ''; ?>><?php echo anchor($myDiscussions, '/discussions/mine', 'MyDiscussions TabLink'); ?></li>
                <?php
                }
                if ($countDrafts > 0 || $sender->ControllerName == 'draftscontroller') {
                    ?>
                    <li<?php echo $sender->ControllerName == 'draftscontroller' ? ' class="Active"' : ''; ?>><?php echo anchor($myDrafts, '/drafts', 'MyDrafts TabLink'); ?></li>
                <?php
                }
                $sender->fireEvent('AfterDiscussionTabs');
                ?>
            </ul>
        </div>
    <?php
    }
endif;

if (!function_exists('optionsList')) :
    /**
     * Build HTML for discussions options menu.
     *
     * @param $discussion
     * @return DropdownModule|string
     * @throws Exception
     */
    function optionsList($discussion) {
        if (Gdn::session()->isValid() && !empty(Gdn::controller()->ShowOptions)) {
            include_once Gdn::controller()->fetchViewLocation('helper_functions', 'discussion', 'vanilla');
            return getDiscussionOptionsDropdown($discussion);
        }
        return '';
    }
endif;

if (!function_exists('writeOptions')) :
    /**
     * Render options that the user has for this discussion.
     */
    function writeOptions($discussion) {
        if (!Gdn::session()->isValid() || !Gdn::controller()->ShowOptions)
            return;

        echo '<span class="Options">';

        // Options list.
        echo optionsList($discussion);

        // Bookmark button.
        echo bookmarkButton($discussion);

        // Admin check.
        echo adminCheck($discussion);

        echo '</span>';
    }
endif;
