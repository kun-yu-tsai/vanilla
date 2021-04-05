<?php

class ArticleHelper implements TemplateHelper {

    /**
     * Take a user object, and writes out an anchor of the user to HTML
     * The write out DOM element is for React to render component.
     *
     * @param array|object $user
     */
    function writeUserAnchor($user) {
        $userFragment = [
            "userID" => $user->UserID ?? $user["UserID"],
            "name" => $user->Name ?? $user["Name"],
            "photoUrl"=> is_object($user) ? userPhotoUrl($user) : $user["Photo"],
        ];
        $userUrl = userUrl($user, "");
        $userUrl = htmlspecialchars(url($userUrl));
        echo "<span class='MItem Author' data-user='".json_encode($userFragment)."' data-url='".$userUrl."'></span>";
    }

    function writeBookmarkAnchor($discussion) {
        $module = Gdn::getContainer()->get(DiscussionModel::class);
        $discussionData = [
            "discussionID" => $discussion->DiscussionID,
            "bookmarked" => $discussion->Bookmarked == '1',
            "countBookmarks" => $module->bookmarkCount($discussion->DiscussionID),
        ];
        echo "<span class='MItem MCount ViewCount' data-discussion='".json_encode($discussionData)."'></span>";
    }

}

?>
