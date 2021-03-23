<?php
class LikeApiController extends AbstractApiController {

    public function patch($id, array $body) {
        $this->permission('Garden.SignIn.Allow');
        switch ($body['type']) {
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
        Gdn::sql()
            ->replace(
                $table,
                [
                    'Liked' =>  $body['like'] ? 1 : 0
                ],
                [
                    'UserID' =>  $this->getSession()->UserID,
                    $column => $id
                ]
            )
        ;
        return [
            'id'=> $id,
            'type'=> $body['type'],
            'liked' => $body['like']
        ];
    }
}
?>
