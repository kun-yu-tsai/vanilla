<?php

if (!isset($Drop)) {
    $Drop = false;
}

if (!isset($Explicit)) {
    $Explicit = false;
}

$Constructor = Gdn::structure();
if (!$Constructor->tableExists(USER_LIKED_DISCUSSION)) {
    $Constructor->table(USER_LIKED_DISCUSSION);
    $Constructor
        ->column('UserID', 'int', false, 'primary')
        ->column('DiscussionID', 'int', false, ['primary', 'key'])
        ->column('Liked', 'tinyint(1)', '0')
        ->set($Explicit, $Drop)
    ;
}

if (!$Constructor->tableExists(USER_LIKED_COMMENT)) {
    $Constructor->table(USER_LIKED_COMMENT);
    $Constructor
        ->column('UserID', 'int', false, 'primary')
        ->column('CommentID', 'int', false, ['primary', 'key'])
        ->column('Liked', 'tinyint(1)', '0')
        ->set($Explicit, $Drop)
    ;
}
?>
