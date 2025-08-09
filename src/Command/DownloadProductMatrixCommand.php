<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:download-product-matrix',
    description: 'Download and import MikroTik product matrix data',
)]
class DownloadProductMatrixCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $httpClient,
        private ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('MikroTik Product Matrix Downloader');

        try {
            $io->section('Downloading product matrix...');
            $csvData = $this->downloadProductMatrix();
            
            $io->section('Parsing and importing data...');
            $useProgress = $output->isDecorated() && $output->isVerbose();
            $importedCount = $this->importProductData($csvData, $io, $useProgress);
            
            $io->success(sprintf('Successfully imported %d products!', $importedCount));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Failed to download or import product matrix: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function downloadProductMatrix(): string
    {
        $urlValue = $this->parameterBag->get('mikrotik.matrix_url');
        if (!is_string($urlValue) || $urlValue === '') {
            throw new \RuntimeException('Invalid or missing config: mikrotik.matrix_url');
        }
        $url = $urlValue;

        $refererValue = $this->parameterBag->get('mikrotik.matrix_referer');
        $referer = is_string($refererValue) ? $refererValue : '';

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Referer' => $referer,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => 'ax=matrix&ax_group='
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to download product matrix: HTTP ' . $response->getStatusCode());
        }

        return $response->getContent();
    }

    private function importProductData(string $csvData, SymfonyStyle $io, bool $useProgress = true): int
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
        
        $lines = explode("\n", $csvData);
        /** @var array<int, string>|null $headers */
        $headers = null;
        $importedCount = 0;

        if ($useProgress) {
            $io->progressStart();
        }

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $fields = str_getcsv($line, ';', '"', '\\');

            if ($lineNumber === 0) {
                $headers = array_map(static fn($v): string => (string) $v, $fields);
                continue;
            }

            if ($headers === null || count($fields) === 2 ) {
                continue; 
            }

            $fieldValues = array_map(static fn($v): string => (string) $v, $fields);
            $data = array_combine($headers, $fieldValues);
            
            if (empty($data['Product code'])) {
                continue;
            }

            $product = $this->createProductFromData($data);
            $this->entityManager->persist($product);
            $importedCount++;

            if ($importedCount % 50 === 0) {
                $this->entityManager->flush();
                if ($useProgress) {
                    $io->progressAdvance(50);
                }
            }
        }

        $this->entityManager->flush();
        if ($useProgress) {
            $io->progressFinish();
            $io->newLine();
        }
        return $importedCount;
    }

    /**
     * @param array<string, string> $data
     */
    private function createProductFromData(array $data): Product
    {
        $product = new Product();

        $product->setProductName($this->cleanValue($data['Product name'] ?? '') ?? '');
        $product->setProductCode($this->cleanValue($data['Product code'] ?? '') ?? '');
        $product->setArchitecture($this->cleanValue($data['Architecture'] ?? ''));
        $product->setCpu($this->cleanValue($data['CPU'] ?? ''));
        $product->setCpuCoreCount($this->parseInteger($data['CPU core count'] ?? ''));
        $product->setCpuNominalFrequency($this->cleanValue($data['CPU nominal frequency'] ?? ''));
        $product->setDimensions($this->cleanValue($data['Dimensions'] ?? ''));
        $product->setLicenseLevel($this->parseInteger($data['License level'] ?? ''));
        $product->setOperatingSystem($this->cleanValue($data['Operating System'] ?? ''));
        $product->setSizeOfRam($this->cleanValue($data['Size of RAM'] ?? ''));
        $product->setStorageSize($this->cleanValue($data['Storage size'] ?? ''));
        $product->setPoeIn($this->cleanValue($data['PoE in'] ?? ''));
        $product->setPoeOut($this->cleanValue($data['PoE out'] ?? ''));
        $product->setPoeOutPorts($this->cleanValue($data['PoE-out ports'] ?? ''));
        $product->setPoeInInputVoltage($this->cleanValue($data['PoE in input Voltage'] ?? ''));
        $product->setNumberOfDcInputs($this->parseInteger($data['Number of DC inputs'] ?? ''));
        $product->setDcJackInputVoltage($this->cleanValue($data['DC jack input Voltage'] ?? ''));
        $product->setMaxPowerConsumption($this->cleanValue($data['Max power consumption'] ?? ''));
        $product->setWireless24GhzNumberOfChains($this->parseInteger($data['Wireless 2.4 GHz number of chains'] ?? ''));
        $product->setAntennaGainDbi24Ghz($this->cleanValue($data['Antenna gain dBi for 2.4 GHz'] ?? ''));
        $product->setWireless5GhzNumberOfChains($this->parseInteger($data['Wireless 5 GHz number of chains'] ?? ''));
        $product->setAntennaGainDbi5Ghz($this->cleanValue($data['Antenna gain dBi for 5 GHz'] ?? ''));
        $product->setEthernet100Ports($this->parseInteger($data['10/100 Ethernet ports'] ?? ''));
        $product->setEthernet1000Ports($this->parseInteger($data['10/100/1000 Ethernet ports'] ?? ''));
        $product->setNumberOfEthernet25GPorts($this->parseInteger($data['Number of 2.5G Ethernet ports'] ?? ''));
        $product->setNumberOfUsbPorts($this->parseInteger($data['Number of USB ports'] ?? ''));
        $product->setEthernetComboPorts($this->cleanValue($data['Ethernet Combo ports'] ?? ''));
        $product->setSfpPorts($this->parseInteger($data['SFP ports'] ?? ''));
        $product->setSfpPlusPorts($this->parseInteger($data['SFP+ ports'] ?? ''));
        $product->setNumberOfHighSpeedEthernetPorts($this->parseInteger($data['Number of 1G/2.5G/5G/10G Ethernet ports'] ?? ''));
        $product->setNumberOfSimSlots($this->parseInteger($data['Number of SIM slots'] ?? ''));
        $product->setMemoryCards($this->cleanValue($data['Memory Cards'] ?? ''));
        $product->setUsbSlotType($this->cleanValue($data['USB slot type'] ?? ''));
        $product->setSuggestedPriceUsd($this->parseFloat($data['Suggested price (USD)'] ?? ''));

        return $product;
    }

    private function cleanValue(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || $value === 'No' || $value === 'None' || $value === '-') {
            return null;
        }
        return $value;
    }

    private function parseInteger(string $value): ?int
    {
        $value = $this->cleanValue($value);
        if ($value === null) {
            return null;
        }
        
        if (preg_match('/(\d+)/', $value, $matches)) {
            return (int) $matches[1];
        }
        
        return null;
    }

    private function parseFloat(string $value): ?float
    {
        $value = $this->cleanValue($value);
        if ($value === null) {
            return null;
        }
        
        if (preg_match('/(\d+\.?\d*)/', $value, $matches)) {
            return (float) $matches[1];
        }
        
        return null;
    }
}
