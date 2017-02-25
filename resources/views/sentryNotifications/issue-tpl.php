Message: <?= $payload['message'] ?>


### Request

URL: <?= $http['url'] ?>

Method: <?= $http['method'] ?>

Query: <?= $http['query_string'] ?: '-' ?>


### User

<?php foreach ($user as $key => $value) : ?>
<?= $key == 'id' ? 'ID' : ucfirst($key) ?>: <?= $value , "\n" ?>
<?php endforeach ?>

<?php if ($payload['event']['extra']) : ?>
### Additional Data

```php
<?= var_export($payload['event']['extra']), "\n" ?>
```
<?php endif ?>

View the event: <?= $payload['url'] ?>


---

**关闭issue后记得关闭sentry的event**
