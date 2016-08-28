<?php

namespace miaoxing\build\services;

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
    protected $travisToken;

    protected $githubToken;

    protected $githubRepo;

    protected $issueLabels = ['bug'];

    protected $failedStatuses = [
        'Broken',
        'Failed',
        'Still Failing'
    ];

    public function getTravisToken()
    {
        return $this->travisToken;
    }

    public function isValidRequest()
    {
        $repoSlug = $this->request->getServer('HTTP_TRAVIS_REPO_SLUG');
        $authorization = $this->request->getServer('HTTP_AUTHORIZATION');
        $digest = hash('sha256', $repoSlug . $this->getTravisToken());
        return $digest == $authorization;
    }

    public function isFailed($status)
    {
        return in_array($status, $this->failedStatuses);
    }

    public function createIssue(array $payload)
    {
        $title = $payload['status_message'] . ': ' . $payload['message'];
        $body = $this->view->render('@build/travisNotifications/issue-tpl.php', ['payload' => $payload]);
        $assignees = $payload['committer_name'];

        if ($this->githubRepo) {
            $repo = $this->githubRepo;
        } else {
            $repo = $payload['repository']['owner_name'] . '/' . $payload['repository']['name'];
        }

        $http = $this->http([
            'url' => 'https://api.github.com/repos/' . $repo . '/issues',
            'method' => 'post',
            'dataType' => 'json',
            'throwException' => false,
            'userAgent' => 'Wei/0.9.X',
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
