<?php

declare(strict_types=1);
// Tambahkan di baris paling atas web/index.php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// comment out the following two lines when deployed to production
// defined('YII_DEBUG') or define('YII_DEBUG', true);
// defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
// Muat environment variables dari .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Definisikan konstanta Yii
defined('YII_DEBUG') or define('YII_DEBUG', isset($_ENV['YII_DEBUG']) && $_ENV['YII_DEBUG'] === 'true');
defined('YII_ENV') or define('YII_ENV', $_ENV['YII_ENV'] ?? 'prod');
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
