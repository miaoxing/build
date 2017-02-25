<?php

namespace Miaoxing\Build\Service;

use miaoxing\plugin\BaseService;
use Wei\Http;
use Wei\Request;
use Wei\RetTrait;
use Wei\View;

/**
 * @method Http http(array $options)
 * @property Request $request
 * @property View $view
 */
class SentryNotification extends BaseService
{
    use RetTrait;

    protected $githubToken = '';

    protected $githubRepo = '';

    protected $assignees = '';

    protected $issueLabels = ['bug'];

    protected function guestAssignees(array $payload)
    {
        if (!isset($payload['event']['sentry.interfaces.Exception'])) {
            return $this->assignees;
        }

        $value = $payload['event']['sentry.interfaces.Exception']['values'][0];
        list($file, $line) = explode(':', $value['module']);

        exec(sprintf('git log -1 --pretty=%%an -L %s,%s:%s', $line, $line, $file), $output);
        if (isset($output[0])) {
            return $output[0];
        }

        return $this->assignees;
    }

    /**
     * @param string $payload
     * @return array
     */
    public function createIssue($payload)
    {
        $payload = json_decode($payload, true);
        if (!$payload || !isset($payload['message'])) {
            return $this->err('Invalid payload');
        }

        $title = sprintf('[%s]Alert: %s', date('y-m-d'), $payload['message']);

        $http = $payload['event']['sentry.interfaces.Http'];
        $user = $payload['event']['sentry.interfaces.User'];
        $body = $this->view->render('sentryNotifications/issue-tpl.php', [
            'payload' => $payload,
            'http' => $http,
            'user' => $user,
        ]);

        $assignees = $this->guestAssignees($payload);

        $http = $this->http([
            'url' => 'https://api.github.com/repos/' . $this->githubRepo . '/issues',
            'method' => 'post',
            'dataType' => 'json',
            'userAgent' => 'Wei/0.9.X',
            'errorLevel' => 'debug',
            'headers' => [
                'Authorization' => 'token ' . $this->githubToken,
            ],
            'data' => json_encode([
                'title' => $title,
                'body' => $body,
                'assignees' => [$assignees],
                'labels' => $this->issueLabels,
            ]),
        ]);

        if ($http->isSuccess()) {
            return $this->suc(['http' => $http]);
        } else {
            return $this->err(['http' => $http]);
        }
    }
}
