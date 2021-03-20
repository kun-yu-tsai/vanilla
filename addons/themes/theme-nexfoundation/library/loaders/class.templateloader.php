<?php

interface PageControllerWithRedux {
    public function addReduxAction(ReduxAction $action): self;
}

interface TemplateLoader {
    /**
     * @param PageControllerWithRedux $sender
     */
    public function load($sender);
}
?>
