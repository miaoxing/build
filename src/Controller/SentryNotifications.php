<?php

namespace Miaoxing\Build\Controller;

use Wei\BaseController;

class SentryNotifications extends BaseController
{
    public function createAction()
    {
        $ret = wei()->sentryNotification->createIssue($this->request->getContent());

        return $this->response->json($ret);
    }
}
