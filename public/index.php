<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

// Gate the app if installer still exists
if (is_dir(__DIR__ . '/install')) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Installation required. Visit /install/ and remove the install directory when done.";
    exit(0);
}

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new Kernel('prod', false);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
