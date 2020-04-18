<?php

require 'functions.php';

$wei = init();

// 2. 初始化数据库
$db = $wei->db;

// 3. 先清空数据表,确保不会受Data truncated for column之类的影响
$tables = getTables();
foreach ($tables as $table) {
    if ('migrations' == $table['TABLE_NAME']) {
        continue;
    }
    $wei->db->query('TRUNCATE TABLE ' . $table['TABLE_NAME']);
}

// 4. 运行全部rollback的SQL
$migrations = $wei->migration->getStatus();
try {
    $wei->migration->rollback([
        'target' => $migrations[0]['id']
    ]);
} catch (\Exception $e) {
    return build_err((string) $e);
}

// 5. 检查数据表
$allowTables = ['apps', 'migrations', 'users'];
$leftTables = array_column(getTables(), 'TABLE_NAME');
$leftTables = array_diff($leftTables, $allowTables);
if ($leftTables) {
    build_err('运行 rollback 后存在未删除的数据表: ' . implode(', ', $leftTables));
} else {
    build_suc('运行 rollback 成功');
}
