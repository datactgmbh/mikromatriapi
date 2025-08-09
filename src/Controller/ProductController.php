<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/api/product/{code}', name: 'api_product_by_code', methods: ['GET'])]
    public function getByCode(string $code): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Product::class);
        $product = $repository->findOneBy(['productCode' => $code]);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse($this->serializeProduct($product));
    }

    #[Route('/api/products', name: 'api_products', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $architecture = $request->query->get('architecture');
        $limit = $request->query->getInt('limit', 100);
        $offset = $request->query->getInt('offset', 0);

        if ($limit < 1) {
            $limit = 1;
        }
        if ($limit > 500) {
            $limit = 500;
        }
        if ($offset < 0) {
            $offset = 0;
        }

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->orderBy('p.productCode', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if (!empty($architecture)) {
            $queryBuilder
                ->andWhere('p.architecture = :architecture')
                ->setParameter('architecture', $architecture);
        }

        /** @var Product[] $products */
        $products = $queryBuilder->getQuery()->getResult();
        $items = array_map(fn (Product $p) => $this->serializeProduct($p), $products);

        return new JsonResponse([
            'count' => count($items),
            'items' => $items,
        ]);
    }

    #[Route('/api/product/{code}/architecture', name: 'api_product_architecture', methods: ['GET'])]
    public function getArchitecture(string $code): JsonResponse
    {
        $repository = $this->entityManager->getRepository(Product::class);
        $product = $repository->findOneBy(['productCode' => $code]);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], 404);
        }

        return new JsonResponse([
            'productCode' => $code,
            'architecture' => $this->formatArchitecture($product->getArchitecture()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeProduct(Product $product): array
    {
        return [
            'productName' => $product->getProductName(),
            'productCode' => $product->getProductCode(),
            'architecture' => $this->formatArchitecture($product->getArchitecture()),
            'cpu' => $product->getCpu(),
            'cpuCoreCount' => $product->getCpuCoreCount(),
            'cpuNominalFrequency' => $product->getCpuNominalFrequency(),
            'dimensions' => $product->getDimensions(),
            'licenseLevel' => $product->getLicenseLevel(),
            'operatingSystem' => $product->getOperatingSystem(),
            'sizeOfRam' => $product->getSizeOfRam(),
            'storageSize' => $product->getStorageSize(),
            'poeIn' => $product->getPoeIn(),
            'poeOut' => $product->getPoeOut(),
            'poeOutPorts' => $product->getPoeOutPorts(),
            'poeInInputVoltage' => $product->getPoeInInputVoltage(),
            'numberOfDcInputs' => $product->getNumberOfDcInputs(),
            'dcJackInputVoltage' => $product->getDcJackInputVoltage(),
            'maxPowerConsumption' => $product->getMaxPowerConsumption(),
            'wireless24GhzNumberOfChains' => $product->getWireless24GhzNumberOfChains(),
            'antennaGainDbi24Ghz' => $product->getAntennaGainDbi24Ghz(),
            'wireless5GhzNumberOfChains' => $product->getWireless5GhzNumberOfChains(),
            'antennaGainDbi5Ghz' => $product->getAntennaGainDbi5Ghz(),
            'ethernet100Ports' => $product->getEthernet100Ports(),
            'ethernet1000Ports' => $product->getEthernet1000Ports(),
            'numberOfEthernet25GPorts' => $product->getNumberOfEthernet25GPorts(),
            'numberOfUsbPorts' => $product->getNumberOfUsbPorts(),
            'ethernetComboPorts' => $product->getEthernetComboPorts(),
            'sfpPorts' => $product->getSfpPorts(),
            'sfpPlusPorts' => $product->getSfpPlusPorts(),
            'numberOfHighSpeedEthernetPorts' => $product->getNumberOfHighSpeedEthernetPorts(),
            'numberOfSimSlots' => $product->getNumberOfSimSlots(),
            'memoryCards' => $product->getMemoryCards(),
            'usbSlotType' => $product->getUsbSlotType(),
            'suggestedPriceUsd' => $product->getSuggestedPriceUsd(),
        ];
    }

    private function formatArchitecture(?string $architecture): string
    {
        $raw = $architecture ?? '';
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }

        $lower = strtolower($trimmed);
        $nospace = preg_replace('/\s+/', '', $lower);

        if ($nospace === 'arm32bit' || $lower === 'arm 32' || $lower === 'arm32') {
            return 'arm';
        }
        if ($nospace === 'arm64bit' || $lower === 'arm 64' || $lower === 'arm64') {
            return 'arm64';
        }

        if ($lower === 'mipsbe' || $lower === 'smips' || $lower === 'mmips') {
            return $lower;
        }

        return $lower;
    }
}


