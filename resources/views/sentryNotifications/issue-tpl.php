Message: <?= $payload['message'] ?>


### Request

URL: <?= $http['url'] ?>

Method: <?= $http['method'] ?>

Query: <?= $http['query_string'] ?: '-' ?>


### User

<?php foreach ($user as $key => $value) : ?>
<?= ucwords(strtr($key, '_', ' ')) ?>: <?= is_scalar($value) ? $value : json_encode($value) , "\n" ?>
<?php endforeach ?>

<?php if ($payload['event']['extra']) : ?>
### Additional Data

```php
<?= var_export($payload['event']['extra']), "\n" ?>
```
<?php endif ?>

View the event: <?= $payload['url'] ?>


---

处理方法参照文档 **"线上告警处理指南V2"**
