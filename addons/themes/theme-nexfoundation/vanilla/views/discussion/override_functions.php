<?php

if (!function_exists('WriteComment')) {
    /**
     * Outputs a formatted comment.
     *
     * Prior to 2.1, this also output the discussion ("FirstComment") to the browser.
     * That has moved to the discussion.php view.
     *
     * @param DataSet $comment .
     * @param Gdn_Controller $sender .
     * @param Gdn_Session $session .
     * @param int $CurrentOffet How many comments into the discussion we are (for anchors).
     */
    function writeComment($comment, $sender, $session, $currentOffset) {
        // Whether to order the name & photo with the latter first.
        static $userPhotoFirst = null;

        $comment = (is_array($comment)) ? (object)$comment: $comment;

        if ($userPhotoFirst === null) {
            $userPhotoFirst = c('Vanilla.Comment.UserPhotoFirst', true);
        }
        $author = Gdn::userModel()->getID($comment->InsertUserID); //UserBuilder($Comment, 'Insert');
        $permalink = val('Url', $comment, '/discussion/comment/'.$comment->CommentID.'/#Comment_'.$comment->CommentID);

        // Set CanEditComments (whether to show checkboxes)
        if (!property_exists($sender, 'CanEditComments')) {
            $sender->CanEditComments = $session->checkPermission('Vanilla.Comments.Edit', true, 'Category', 'any') && c('Vanilla.AdminCheckboxes.Use');
        }
        // Prep event args
        $cssClass = cssClass($comment, false);
        $sender->EventArguments['Comment'] = &$comment;
        $sender->EventArguments['Author'] = &$author;
        $sender->EventArguments['CssClass'] = &$cssClass;
        $sender->EventArguments['CurrentOffset'] = $currentOffset;
        $sender->EventArguments['Permalink'] = $permalink;

        // Needed in writeCommentOptions()
        if ($sender->data('Discussion', null) === null) {
            $discussionModel = new DiscussionModel();
            $discussion = $discussionModel->getID($comment->DiscussionID);
            $sender->setData('Discussion', $discussion);
        }

        if ($sender->data('Discussion.InsertUserID') === $comment->InsertUserID) {
            $cssClass .= ' isOriginalPoster';
        }

        // DEPRECATED ARGUMENTS (as of 2.1)
        $sender->EventArguments['Object'] = &$comment;
        $sender->EventArguments['Type'] = 'Comment';

        // First comment template event
        $sender->fireEvent('BeforeCommentDisplay'); ?>
        <li class="<?php echo $cssClass; ?>" id="<?php echo 'Comment_'.$comment->CommentID; ?>">
            <div class="Comment">

                <?php
                // Write a stub for the latest comment so it's easy to link to it from outside.
                if ($currentOffset == Gdn::controller()->data('_LatestItem') && Gdn::config('Vanilla.Comments.AutoOffset')) {
                    echo '<span id="latest"></span>';
                }
                ?>
                <div class="Item-Floor">
                    <?php echo $currentOffset.t('floor', '樓');?>
                </div>
                <div class="Item-BodyWrap">
                    <div class="Item-Body">
                        <div class="Message userContent">
                            <div class="CommentBody">
                            <?php
                            echo formatBody($comment);
                            ?>
                            </div>
                            <div class="Options">
                                <?php writeCommentOptions($comment); ?>
                            </div>
                        </div>
                    </div>
                    <div class="Item-Footer">
                        <?php
                            $sender->fireEvent('AfterCommentBody');
                            writeReactions($comment);
                            if (val('Attachments', $comment)) {
                                writeAttachments($comment->Attachments);
                            }
                        ?>
                        <?php $sender->fireEvent('BeforeCommentMeta'); ?>
                        <div class="Item-Header CommentHeader">
                            <div class="AuthorWrap">
                                <span class="Author">
                                    <?php
                                    if ($userPhotoFirst) {
                                        echo userPhoto($author);
                                        echo userAnchor($author, 'Username');
                                    } else {
                                        echo userAnchor($author, 'Username');
                                        echo userPhoto($author);
                                    }
                                    echo formatMeAction($comment);
                                    $sender->fireEvent('AuthorPhoto');
                                    ?>
                                </span>
                                <span class="AuthorInfo">
                                    <?php
                                    echo ' '.wrapIf(htmlspecialchars(val('Title', $author)), 'span', ['class' => 'MItem AuthorTitle']);
                                    echo ' '.wrapIf(htmlspecialchars(val('Location', $author)), 'span', ['class' => 'MItem AuthorLocation']);
                                    $sender->fireEvent('AuthorInfo');
                                    ?>
                                </span>
                            </div>
                            <div class="Meta CommentMeta CommentInfo">
                                <span class="MItem DateCreated">
                                    <?php echo anchor(Gdn_Format::date($comment->DateInserted, 'html'), $permalink, 'Permalink', ['name' => 'Item_'.($currentOffset), 'rel' => 'nofollow']); ?>
                                </span>
                                <?php
                                echo dateUpdated($comment, ['<span class="MItem">', '</span>']);
                                ?>
                                <?php
                                // Include source if one was set
                                if ($source = val('Source', $comment)) {
                                    echo wrap(sprintf(t('via %s'), t($source.' Source', $source)), 'span', ['class' => 'MItem Source']);
                                }

                                // Include IP Address if we have permission
                                // if ($session->checkPermission('Garden.PersonalInfo.View')) {
                                //     echo wrap(ipAnchor($comment->InsertIPAddress), 'span', ['class' => 'MItem IPAddress']);
                                // }

                                $sender->fireEvent('CommentInfo');
                                $sender->fireEvent('InsideCommentMeta'); // DEPRECATED
                                $sender->fireEvent('AfterCommentMeta'); // DEPRECATED
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php
        $sender->fireEvent('AfterComment');
    }
}