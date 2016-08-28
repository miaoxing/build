<?php

namespace miaoxing\build\controllers;

use miaoxing\build\services\TravisNotification;
use Wei\BaseController;
use Wei\Logger;
use Wei\Request;
use Wei\Response;

/**
 * @property TravisNotification $travisNotification
 * @property Logger $logger
 */
class TravisNotifications extends BaseController
{
    public function createAction(Request $req, Response $res)
    {
        $notify = $this->travisNotification;
        if (!$notify->isValidRequest()) {
            return $this->response('Invalid authorization', 401);
        }

        $payload = json_decode($req['payload'], true);
        if (!$notify->isFailed($payload['status_message'])) {
            return $this->response('Build ' . $payload['status']);
        }

        if (!wei()->cache->add('travisPayload:' . $payload['id'], true, 86400)) {
            return $this->response('Payload ' . $payload['id'] . ' processed');
        }

        $http = $notify->createIssue($payload);
        return $this->response($http->getResponseText(), $http->getCurlInfo(CURLINFO_HTTP_CODE));
    }

    protected function response($content, $status = 200)
    {
        $level = in_array($status, [200, 201, 401]) ? 'info' : 'warning';
        $this->logger->log($level, $content);

        $this->response->setContent($content)->setStatusCode($status);
        return $this->response;
    }
}
