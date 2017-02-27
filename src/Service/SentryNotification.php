<?php

namespace Miaoxing\Build\Service;

use miaoxing\plugin\BaseService;
use Wei\Http;
use Wei\Logger;
use Wei\Request;
use Wei\RetTrait;
use Wei\View;

/**
 * @method Http http(array $options)
 * @property Request $request
 * @property View $view
 * @property Logger $logger
 */
class SentryNotification extends BaseService
{
    use RetTrait;

    protected $githubToken = '';

    protected $githubRepo = '';

    protected $assignees = '';

    protected $issueLabels = ['bug'];

    protected $validAssignees = [];

    protected $ignorePaths = [];

    protected function guestAssignees(array $payload)
    {
        if (!isset($payload['event']['sentry.interfaces.Exception'])) {
            return $this->assignees;
        }

        $value = $payload['event']['sentry.interfaces.Exception']['values'][0];
        list($file, $line) = explode(':', $value['module']);

        $file = substr($file, strlen($payload['event']['sentry.interfaces.Http']['env']['DOCUMENT_ROOT']) + 1);
        foreach ($this->ignorePaths as $path) {
            if (substr($file, 0, strlen($path)) == $path) {
                return $this->assignees;
            }
        }

        // TODO webhook路径和项路径不一致
        $file = '../' . $file;

        $result = exec(sprintf('git blame -L%s,+1 %s', $line, $file), $output, $return);
        if (!$result) {
            $this->logger->info('Invalid git blame result', compact('output', 'return', 'result', 'line', 'file'));

            return $this->assignees;
        }

        // ^f19ba07 xxx/xxx.php (author 0000-00-00 00:00:00 +0800 xxx
        preg_match('/\((.+?) /', $result, $matches);
        if (isset($matches[1]) && $this->isAssignerValid($matches[1])) {
            return $matches[1];
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

        $this->logger->info('Received payload', ['payload' => $payload]);

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

    protected function isAssignerValid($assignee)
    {
        if (!$this->validAssignees) {
            return true;
        }

        return in_array($assignee, $this->validAssignees);
    }
}
