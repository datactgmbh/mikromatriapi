<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\DownloadProductMatrixCommand;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[CoversClass(DownloadProductMatrixCommand::class)]
final class DownloadProductMatrixCommandTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($em);
        $tool->dropSchema($metadata);
        $tool->createSchema($metadata);
    }

    public function testImportsCsvIntoDatabase(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        /** @var ParameterBagInterface $params */
        $params = static::getContainer()->get(ParameterBagInterface::class);

        $http = $this->createMock(HttpClientInterface::class);

        $csv = "Product name;Product code;Architecture\nRouter X;RBX;ARM\n";
        $response = new class($csv) implements ResponseInterface {
            public function __construct(private string $csv) {}
            public function getStatusCode(): int { return 200; }
            public function getHeaders(bool $throw = true): array { return []; }
            public function getContent(bool $throw = true): string { return $this->csv; }
            public function toArray(bool $throw = true): array { return []; }
            public function cancel(): void {}
            public function getInfo(string $type = null): mixed { return null; }
        };
        $http->method('request')->willReturn($response);

        $command = new DownloadProductMatrixCommand($em, $http, $params);
        $input = new \Symfony\Component\Console\Input\ArrayInput([]);
        $output = new \Symfony\Component\Console\Output\BufferedOutput(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL, false);
        $exitCode = $command->run($input, $output);
        $display = $output->fetch();

        self::assertSame(0, $exitCode, $display);
        self::assertStringContainsString('Successfully imported 1 products', $display);

        // Verify persistence in the test database
        $repo = $em->getRepository(Product::class);
        $all = $repo->findAll();
        self::assertCount(1, $all);
        self::assertSame('RBX', $all[0]->getProductCode());
    }

    protected function tearDown(): void
    {
        if (static::$kernel !== null) {
            /** @var EntityManagerInterface $em */
            $em = static::getContainer()->get('doctrine')->getManager();
            $schemaTool = new SchemaTool($em);
            $metadata = $em->getMetadataFactory()->getAllMetadata();
            $schemaTool->dropSchema($metadata);
        }
        parent::tearDown();
    }
}


