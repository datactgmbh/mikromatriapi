<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class ProductControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return \App\Kernel::class;
    }

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get('doctrine')->getManager();
        $this->entityManager = $em;

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testGetByCodeReturns404WhenMissing(): void
    {
        $this->client->request('GET', '/api/product/UNKNOWN');
        self::assertResponseStatusCodeSame(404);
        self::assertJson($this->client->getResponse()->getContent());
    }

    public function testGetByCodeReturnsProduct(): void
    {
        $product = (new Product())
            ->setProductName('Router')
            ->setProductCode('RB123')
            ->setArchitecture('ARM');

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/product/RB123');
        self::assertResponseIsSuccessful();
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('RB123', $data['productCode']);
        self::assertSame('Router', $data['productName']);
    }

    public function testListProductsWithFiltersAndPagination(): void
    {
        foreach ([
            ['RB100', 'ARM'],
            ['RB101', 'MIPS'],
            ['RB102', 'ARM'],
        ] as [$code, $arch]) {
            $p = (new Product())
                ->setProductName('P ' . $code)
                ->setProductCode($code)
                ->setArchitecture($arch);
            $this->entityManager->persist($p);
        }
        $this->entityManager->flush();

        $this->client->request('GET', '/api/products?architecture=ARM&limit=1&offset=0');
        self::assertResponseIsSuccessful();
        $payload = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(1, $payload['count']);
        self::assertCount(1, $payload['items']);

        $this->client->request('GET', '/api/products?architecture=ARM&limit=2&offset=0');
        self::assertResponseIsSuccessful();
        $payload = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(2, $payload['count']);
        self::assertCount(2, $payload['items']);
    }

    protected function tearDown(): void
    {

        parent::tearDown();
    }
}


