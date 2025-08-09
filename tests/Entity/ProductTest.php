<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testDefaultTimestampsAreInitialized(): void
    {
        $product = new Product();
        self::assertInstanceOf(\DateTimeInterface::class, $product->getCreatedAt());
        self::assertInstanceOf(\DateTimeInterface::class, $product->getUpdatedAt());
    }

    public function testGettersAndSetters(): void
    {
        $product = (new Product())
            ->setProductName('RouterBoard X')
            ->setProductCode('RBX')
            ->setArchitecture('ARM')
            ->setCpu('QuadCore')
            ->setCpuCoreCount(4)
            ->setCpuNominalFrequency('1.4GHz')
            ->setDimensions('120x80x25mm')
            ->setLicenseLevel(4)
            ->setOperatingSystem('RouterOS')
            ->setSizeOfRam('256MB')
            ->setStorageSize('16MB')
            ->setPoeIn('802.3af')
            ->setPoeOut('Passive')
            ->setPoeOutPorts('PoE-Out on Ether5')
            ->setPoeInInputVoltage('18-57 V')
            ->setNumberOfDcInputs(1)
            ->setDcJackInputVoltage('9-30 V')
            ->setMaxPowerConsumption('18 W')
            ->setWireless24GhzNumberOfChains(2)
            ->setAntennaGainDbi24Ghz('2 dBi')
            ->setWireless5GhzNumberOfChains(2)
            ->setAntennaGainDbi5Ghz('3 dBi')
            ->setEthernet100Ports(5)
            ->setEthernet1000Ports(1)
            ->setNumberOfEthernet25GPorts(0)
            ->setNumberOfUsbPorts(1)
            ->setEthernetComboPorts('1x combo')
            ->setSfpPorts(0)
            ->setSfpPlusPorts(0)
            ->setNumberOfHighSpeedEthernetPorts(0)
            ->setNumberOfSimSlots(0)
            ->setMemoryCards('microSD')
            ->setUsbSlotType('USB-A')
            ->setSuggestedPriceUsd(129.99);

        self::assertSame('RouterBoard X', $product->getProductName());
        self::assertSame('RBX', $product->getProductCode());
        self::assertSame('ARM', $product->getArchitecture());
        self::assertSame('QuadCore', $product->getCpu());
        self::assertSame(4, $product->getCpuCoreCount());
        self::assertSame('1.4GHz', $product->getCpuNominalFrequency());
        self::assertSame('120x80x25mm', $product->getDimensions());
        self::assertSame(4, $product->getLicenseLevel());
        self::assertSame('RouterOS', $product->getOperatingSystem());
        self::assertSame('256MB', $product->getSizeOfRam());
        self::assertSame('16MB', $product->getStorageSize());
        self::assertSame('802.3af', $product->getPoeIn());
        self::assertSame('Passive', $product->getPoeOut());
        self::assertSame('PoE-Out on Ether5', $product->getPoeOutPorts());
        self::assertSame('18-57 V', $product->getPoeInInputVoltage());
        self::assertSame(1, $product->getNumberOfDcInputs());
        self::assertSame('9-30 V', $product->getDcJackInputVoltage());
        self::assertSame('18 W', $product->getMaxPowerConsumption());
        self::assertSame(2, $product->getWireless24GhzNumberOfChains());
        self::assertSame('2 dBi', $product->getAntennaGainDbi24Ghz());
        self::assertSame(2, $product->getWireless5GhzNumberOfChains());
        self::assertSame('3 dBi', $product->getAntennaGainDbi5Ghz());
        self::assertSame(5, $product->getEthernet100Ports());
        self::assertSame(1, $product->getEthernet1000Ports());
        self::assertSame(0, $product->getNumberOfEthernet25GPorts());
        self::assertSame(1, $product->getNumberOfUsbPorts());
        self::assertSame('1x combo', $product->getEthernetComboPorts());
        self::assertSame(0, $product->getSfpPorts());
        self::assertSame(0, $product->getSfpPlusPorts());
        self::assertSame(0, $product->getNumberOfHighSpeedEthernetPorts());
        self::assertSame(0, $product->getNumberOfSimSlots());
        self::assertSame('microSD', $product->getMemoryCards());
        self::assertSame('USB-A', $product->getUsbSlotType());
        self::assertSame(129.99, $product->getSuggestedPriceUsd());
    }
}


