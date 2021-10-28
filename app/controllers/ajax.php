<?php
/**
 * An example of an ajax controller.
 */



// make sure an action is specified.
if (!$this->request->requestVarExists('action')) {
    $this->present_json(400, "Missing action argument.");
    return;
}



// get the action.
$action	= $this->request->requestVar('action');



// switch over the action.
switch ($action) {
    case 'ping':
        $this->present_json(200, 'pong');
        return;
}



// provide a default response.
$this->present_json(400, 'Action not recognised.');