<?php

namespace miaoxing\build\controllers;

use miaoxing\build\services\TravisNotification;
use Wei\BaseController;
use Wei\Request;
use Wei\Response;

/**
 * @property TravisNotification $travisNotification
 */
class TravisNotifications extends BaseController
{
    public function createAction(Request $req, Response $res)
    {
        $notify = $this->travisNotification;

        echo $this->request->getContent() . PHP_EOL;

        if (!$notify->isValidRequest()) {
            return $res->setContent('Invalid authorization')->setStatusCode(401);
        }

        $repoSlug = $req->getServer('HTTP_TRAVIS_REPO_SLUG');
        $payload = json_decode($req['payload'], true);
        echo 'Received valid payload for repository ' . $repoSlug . PHP_EOL;

        if (!$notify->isFailed($payload['status_message'])) {
            return $res->setContent('Build ' . $payload['status']);
        }

        $http = $notify->createIssue($payload);
        return $res->setContent($http->getResponseText())->setStatusCode($http->getCurlInfo(CURLINFO_HTTP_CODE));
    }
}
