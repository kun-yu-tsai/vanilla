<?php
define('USER_LIKED_DISCUSSION', 'UserLikeDiscussion');
define('USER_LIKED_COMMENT', 'UserLikeComment');

class LikedPostPlugin extends Gdn_Plugin {

    public function __construct() {
        $this->settingsView = 'plugins/LikedPost';
    }

    public function setup() {
        $this->structure();
    }

    public function settingsEndpoint($sender, $args) {
        $sender->permission('Garden.Settings.Manage');
        $configurationModule = new ConfigurationModule($sender);
        $configurationModule->initialize([
            'Feature.LikedPost.RenderPostReaction' => [
                'LabelCode' => t('Render Post Reaction', 'Render Reaction Button to the Post'),
                'Control' => 'Toggle',
                'Description' => t('Render Post Reaction Description')
            ]
        ]);
        $sender->setData('ConfigurationModule', $configurationModule);
        $sender->render('settings', '', $this->settingsView);
    }

    public function gdn_pluginManager_afterStart_handler($sender) {
        $sender->registerCallback("settingsController_likedpost_create", [$this, 'settingsEndpoint']);
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

    private function echo_button(string $recordType, int $recordID) {
        $liked = $this->getRecordLiked($recordType, $recordID);
        $meta = [
            "type" => $recordType,
            "id" => $recordID,
            "liked" => $liked,
            "count" => $this->getRecordLikeCount($recordType, $recordID)
        ];
        $meta = json_encode($meta);
        echo "<div name='likeButton' data-meta='$meta'></div>";
    }

    public function base_afterReactions_handler($sender, $args) {
        if ($args['RecordType'] == 'discussion' && c('Feature.LikedPost.RenderPostReaction')) {
            return;
        }
        $this->echo_button($args['RecordType'], $args['RecordID']);
    }

    public function base_startRenderLikedButton_handler($sender, $args) {
        $this->echo_button($args['RecordType'], $args['RecordID']);
    }

    public function structure() {
        include(dirname(__FILE__)."/settings/structure.php");
    }
}

?>
