<?php
define('USER_LIKED_DISCUSSION', 'UserLikeDiscussion');
define('USER_LIKED_COMMENT', 'UserLikeComment');

class LikedPostPlugin extends Gdn_Plugin {

    public function setup() {
        $this->structure();
    }

    public function getRecordLiked($recordType, $recordID) {
        switch ($recordType) {
            case 'discussion':
                $table = USER_LIKED_DISCUSSION;
                $column = 'DiscussionID';
                break;
            case 'comment':
                $table = USER_LIKED_COMMENT;
                $column = 'CommentID';
                break;
            default:
                return false;
        }
        $likes = Gdn::sql()
            ->select('Liked')
            ->where('UserID', Gdn::session()->UserID)
            ->where($column, $recordID)
            ->from($table)
            ->get()->resultArray();
        $likes = array_column($likes, 'Liked');
        if (count($likes)) {
            return $likes[0] > 0;
        }
        return false;
    }

    public function getRecordLikeCount($recordType, $recordID) {
        switch ($recordType) {
            case 'discussion':
                $table = USER_LIKED_DISCUSSION;
                $column = 'DiscussionID';
                break;
            case 'comment':
                $table = USER_LIKED_COMMENT;
                $column = 'CommentID';
                break;
            default:
                return false;
        }
        $likes = Gdn::sql()
            ->select('Liked')
            ->where($column, $recordID)
            ->where('Liked', 1)
            ->from($table)
            ->get()->resultArray();
        return count($likes);
    }

    public function base_afterReactions_handler($sender, $args) {

        $liked = $this->getRecordLiked($args['RecordType'], $args['RecordID']);

        $meta = [
            "type" => $args['RecordType'],
            "id" => $args['RecordID'],
            "liked" => $liked,
            "count" => $this->getRecordLikeCount($args['RecordType'], $args['RecordID'])
        ];
        $meta = json_encode($meta);
        echo "<div name='likeButton' data-meta='$meta'></div>";
    }

    public function structure() {
        include(dirname(__FILE__)."/settings/structure.php");
    }
}

?>
