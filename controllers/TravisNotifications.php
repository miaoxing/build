<?php

namespace miaoxing\build\controllers;

use miaoxing\build\TravisNotification;
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

        echo $this->request->getContent();

        if(!$notify->isValidRequest()) {
            return $res->send('Invalid authorization', 401);
        }

        $repoSlug = $req->getServer('HTTP_TRAVIS_REPO_SLUG');
        $payload = json_decode($req['payload'], true);
        echo 'Received valid payload for repository ' . $repoSlug;

        if (!$notify->isFailed($payload['status'])) {
            return $res->send('Build ' . $payload['status']);
        }

        $http = $notify->createIssue($payload);
        return $res->send($http->getResponseText(), $http->getCurlInfo('http_code'));
    }
}