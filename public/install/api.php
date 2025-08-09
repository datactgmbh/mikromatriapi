<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

// Simple endpoint to assist installer.
// - GET: returns available DB drivers and recommendations
// - POST: writes parameters.local.yaml and runs migrations
// Lives under public/install, outside Symfony's routing.

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $pdoDrivers = [];
    if (class_exists('PDO')) {
        try {
            $pdoDrivers = \PDO::getAvailableDrivers();
        } catch (\Throwable $e) {
            $pdoDrivers = [];
        }
    }
    $drivers = [
        'mysql' => in_array('mysql', $pdoDrivers, true) || extension_loaded('pdo_mysql'),
        'pgsql' => in_array('pgsql', $pdoDrivers, true) || extension_loaded('pdo_pgsql'),
        'sqlite' => in_array('sqlite', $pdoDrivers, true) || extension_loaded('pdo_sqlite'),
    ];
    $projectRoot = dirname(__DIR__, 2);
    $recommendedSqlite = $projectRoot . '/var/app.sqlite';
    echo json_encode([
        'success' => true,
        'drivers' => $drivers,
        'recommendedSqlitePath' => $recommendedSqlite,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'errors' => ['Method not allowed']]);
    exit;
}

function body_param(string $key, $default = ''): string {
    if (isset($_POST[$key])) return (string) $_POST[$key];
    $raw = file_get_contents('php://input');
    if ($raw) {
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data[$key])) {
            return (string) $data[$key];
        }
    }
    return (string) $default;
}

$dbDriver = body_param('db_driver', 'mysql');
$dbHost = trim(body_param('db_host'));
$dbPort = trim(body_param('db_port'));
$dbName = trim(body_param('db_name'));
$dbUser = trim(body_param('db_user'));
$dbPass = body_param('db_pass');
$sqlitePath = trim(body_param('sqlite_path'));
$matrixUrl = trim(body_param('matrix_url', 'https://mikrotik.com/products'));
$matrixReferer = trim(body_param('matrix_referer', 'https://mikrotik.com/products/matrix'));

$errors = [];
$availableDrivers = [];
if (class_exists('PDO')) {
    try {
        $availableDrivers = \PDO::getAvailableDrivers();
    } catch (\Throwable $e) {
        $availableDrivers = [];
    }
}
if (!in_array($dbDriver, ['mysql','pgsql','sqlite'], true)) {
    $errors[] = 'Unsupported database driver.';
}
if ($dbDriver) {
    $pdoHasDriver = in_array($dbDriver, $availableDrivers, true);
    $extHasDriver = extension_loaded('pdo_' . $dbDriver);
    if (!$pdoHasDriver && !$extHasDriver) {
        $errors[] = sprintf('PHP PDO driver for %s is not available.', $dbDriver);
    }
}

if ($dbDriver === 'sqlite') {
    if ($sqlitePath === '') {
        $errors[] = 'SQLite file path is required.';
    }
} else {
    if ($dbHost === '' || $dbName === '' || $dbUser === '') {
        $errors[] = 'Database connection details are required.';
    }
}

header('Content-Type: application/json');
if ($errors) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$charset = $dbDriver === 'pgsql' ? 'utf8' : 'utf8mb4';
$port = $dbPort !== '' ? $dbPort : ($dbDriver === 'pgsql' ? '5432' : '3306');
$scheme = $dbDriver === 'pgsql' ? 'postgresql' : 'mysql';
$appSecret = bin2hex(random_bytes(32));

if ($dbDriver === 'sqlite') {
    $sqliteAbs = $sqlitePath;
    if ($sqliteAbs[0] !== '/' && (!preg_match('/^[A-Za-z]:\\\\/', $sqliteAbs))) {
        $sqliteAbs = dirname(__DIR__, 2) . '/' . ltrim($sqliteAbs, '/');
    }
    $dir = dirname($sqliteAbs);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $url = sprintf('sqlite:///%s', $sqliteAbs);
} else {
    $url = sprintf(
        '%s://%s:%s@%s:%s/%s?charset=%s',
        $scheme,
        rawurlencode($dbUser),
        rawurlencode($dbPass),
        $dbHost,
        $port,
        $dbName,
        $charset
    );
}

$yaml = <<<YAML
parameters:
    app.secret: '%s'
    database.url: '%s'
    mikrotik.matrix_url: '%s'
    mikrotik.matrix_referer: '%s'
YAML;

$content = sprintf($yaml, $appSecret, $url, $matrixUrl, $matrixReferer);

$configDir = dirname(__DIR__, 2) . '/config';
if (!is_dir($configDir)) {
    mkdir($configDir, 0775, true);
}

$target = $configDir . '/parameters.local.yaml';
if (false === file_put_contents($target, $content)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['Failed to write configuration']]);
    exit;
}

$projectRoot = dirname(__DIR__, 2);
try {
    // Ensure project dir context for relative path resolution inside Symfony
    if (is_dir($projectRoot)) {
        @chdir($projectRoot);
    }
    require_once $projectRoot . '/vendor/autoload.php';
    if (file_exists($projectRoot . '/config/bootstrap.php')) {
        require_once $projectRoot . '/config/bootstrap.php';
    }
    $env = 'prod';
    $debug = false;
    $kernelClass = 'App\\Kernel';
    if (!class_exists($kernelClass)) {
        echo json_encode(['success' => true, 'note' => 'Config saved; kernel not found, skipped migrations.']);
        exit;
    }

    $kernel = new $kernelClass($env, $debug);
    $kernel->boot();
    $container = $kernel->getContainer();
    $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
    $application->setAutoExit(false);

    $input = new \Symfony\Component\Console\Input\ArrayInput([
        'command' => 'doctrine:migrations:migrate',
        '--no-interaction' => true,
        '--allow-no-migration' => true,
    ]);
    $output = new \Symfony\Component\Console\Output\BufferedOutput();

    $exitCode = $application->run($input, $output);
    $kernel->shutdown();

    if ($exitCode !== 0) {
        echo json_encode([
            'success' => false,
            'note' => 'Config saved; migrations command failed.',
            'exitCode' => $exitCode,
            'output' => $output->fetch(),
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'note' => 'Config saved; migrations executed.',
        'migrated' => true,
        'output' => $output->fetch(),
    ]);
} catch (\Throwable $e) {
    echo json_encode(['success' => false, 'note' => 'Config saved; migrations could not be executed.', 'error' => $e->getMessage()]);
}

