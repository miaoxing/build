<?php

namespace miaoxing\build;

use miaoxing\plugin\BaseService;
use Wei\Http;
use Wei\Request;
use Wei\View;

/**
 * @method Http http(array $options)
 * @property Request $request
 * @property View $view
 */
class TravisNotification extends BaseService
{
    protected $token;

    protected $githubToken;

    protected $issueLabels = ['bug'];

    protected $failedStatuses = [
        'Broken',
        'Failed',
        'Still Failing'
    ];

    public function getToken()
    {
        return $this->token;
    }

    public function isValidRequest()
    {
        $repoSlug = $this->request->getServer('HTTP_TRAVIS_REPO_SLUG');
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        $digest = hash('sha256', $repoSlug . $this->getToken());
        return $digest == $authorization;
    }

    public function isFailed($status)
    {
        return in_array($status, $this->failedStatuses);
    }

    public function createIssue(array $payload)
    {
        $repo = $payload['repository']['owner_name'] . '/' . $payload['repository']['name'];
        $title = $payload['status'] . ': ' . $payload['message'];
        $body = $this->view->render('@build/travisNotifications/issue-tpl.php', ['payload' => $payload]);
        $assignees = $payload['committer_name'];

        $http = $this->http([
            'url' => 'https://api.github.com/repos/' . $repo . '/issues',
            'method' => 'post',
            'dataType' => 'json',
            'throwException' => false,
            'headers' => [
                'Authorization' => 'token ' . $this->githubToken,
            ],
            'data' => json_encode([
                'title' => $title,
                'body' => $body,
                'assignees' => [$assignees],
                'labels' => $this->issueLabels,
            ])
        ]);
        return $http;
    }
}