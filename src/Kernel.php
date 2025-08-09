<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }
}
