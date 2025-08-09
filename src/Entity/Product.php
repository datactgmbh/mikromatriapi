<?php
/*
 * Copyright 2025 DatACT GmbH 
 * SPDX-License-Identifier: AGPL-3.0
 * 
 * @copyright DatACT GmbH (https://www.datact.ch/)
 * @license https://www.gnu.org/licenses/agpl-3.0-standalone.html
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
#[ORM\Index(columns: ['product_code'], name: 'idx_product_code')]
#[ORM\Index(columns: ['architecture'], name: 'idx_architecture')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $productName = '';

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $productCode = '';

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $architecture = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $cpu = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cpuCoreCount = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $cpuNominalFrequency = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $dimensions = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $licenseLevel = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $operatingSystem = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $sizeOfRam = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $storageSize = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $poeIn = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $poeOut = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $poeOutPorts = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $poeInInputVoltage = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfDcInputs = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $dcJackInputVoltage = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $maxPowerConsumption = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $wireless24GhzNumberOfChains = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $antennaGainDbi24Ghz = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $wireless5GhzNumberOfChains = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $antennaGainDbi5Ghz = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ethernet100Ports = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ethernet1000Ports = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfEthernet25GPorts = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfUsbPorts = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $ethernetComboPorts = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sfpPorts = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sfpPlusPorts = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfHighSpeedEthernetPorts = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfSimSlots = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $memoryCards = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $usbSlotType = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $suggestedPriceUsd = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;
        return $this;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;
        return $this;
    }

    public function getArchitecture(): ?string
    {
        return $this->architecture;
    }

    public function setArchitecture(?string $architecture): static
    {
        $this->architecture = $architecture;
        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(?string $cpu): static
    {
        $this->cpu = $cpu;
        return $this;
    }

    public function getCpuCoreCount(): ?int
    {
        return $this->cpuCoreCount;
    }

    public function setCpuCoreCount(?int $cpuCoreCount): static
    {
        $this->cpuCoreCount = $cpuCoreCount;
        return $this;
    }

    public function getCpuNominalFrequency(): ?string
    {
        return $this->cpuNominalFrequency;
    }

    public function setCpuNominalFrequency(?string $cpuNominalFrequency): static
    {
        $this->cpuNominalFrequency = $cpuNominalFrequency;
        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions): static
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    public function getLicenseLevel(): ?int
    {
        return $this->licenseLevel;
    }

    public function setLicenseLevel(?int $licenseLevel): static
    {
        $this->licenseLevel = $licenseLevel;
        return $this;
    }

    public function getOperatingSystem(): ?string
    {
        return $this->operatingSystem;
    }

    public function setOperatingSystem(?string $operatingSystem): static
    {
        $this->operatingSystem = $operatingSystem;
        return $this;
    }

    public function getSizeOfRam(): ?string
    {
        return $this->sizeOfRam;
    }

    public function setSizeOfRam(?string $sizeOfRam): static
    {
        $this->sizeOfRam = $sizeOfRam;
        return $this;
    }

    public function getStorageSize(): ?string
    {
        return $this->storageSize;
    }

    public function setStorageSize(?string $storageSize): static
    {
        $this->storageSize = $storageSize;
        return $this;
    }

    public function getPoeIn(): ?string
    {
        return $this->poeIn;
    }

    public function setPoeIn(?string $poeIn): static
    {
        $this->poeIn = $poeIn;
        return $this;
    }

    public function getPoeOut(): ?string
    {
        return $this->poeOut;
    }

    public function setPoeOut(?string $poeOut): static
    {
        $this->poeOut = $poeOut;
        return $this;
    }

    public function getPoeOutPorts(): ?string
    {
        return $this->poeOutPorts;
    }

    public function setPoeOutPorts(?string $poeOutPorts): static
    {
        $this->poeOutPorts = $poeOutPorts;
        return $this;
    }

    public function getPoeInInputVoltage(): ?string
    {
        return $this->poeInInputVoltage;
    }

    public function setPoeInInputVoltage(?string $poeInInputVoltage): static
    {
        $this->poeInInputVoltage = $poeInInputVoltage;
        return $this;
    }

    public function getNumberOfDcInputs(): ?int
    {
        return $this->numberOfDcInputs;
    }

    public function setNumberOfDcInputs(?int $numberOfDcInputs): static
    {
        $this->numberOfDcInputs = $numberOfDcInputs;
        return $this;
    }

    public function getDcJackInputVoltage(): ?string
    {
        return $this->dcJackInputVoltage;
    }

    public function setDcJackInputVoltage(?string $dcJackInputVoltage): static
    {
        $this->dcJackInputVoltage = $dcJackInputVoltage;
        return $this;
    }

    public function getMaxPowerConsumption(): ?string
    {
        return $this->maxPowerConsumption;
    }

    public function setMaxPowerConsumption(?string $maxPowerConsumption): static
    {
        $this->maxPowerConsumption = $maxPowerConsumption;
        return $this;
    }

    public function getWireless24GhzNumberOfChains(): ?int
    {
        return $this->wireless24GhzNumberOfChains;
    }

    public function setWireless24GhzNumberOfChains(?int $wireless24GhzNumberOfChains): static
    {
        $this->wireless24GhzNumberOfChains = $wireless24GhzNumberOfChains;
        return $this;
    }

    public function getAntennaGainDbi24Ghz(): ?string
    {
        return $this->antennaGainDbi24Ghz;
    }

    public function setAntennaGainDbi24Ghz(?string $antennaGainDbi24Ghz): static
    {
        $this->antennaGainDbi24Ghz = $antennaGainDbi24Ghz;
        return $this;
    }

    public function getWireless5GhzNumberOfChains(): ?int
    {
        return $this->wireless5GhzNumberOfChains;
    }

    public function setWireless5GhzNumberOfChains(?int $wireless5GhzNumberOfChains): static
    {
        $this->wireless5GhzNumberOfChains = $wireless5GhzNumberOfChains;
        return $this;
    }

    public function getAntennaGainDbi5Ghz(): ?string
    {
        return $this->antennaGainDbi5Ghz;
    }

    public function setAntennaGainDbi5Ghz(?string $antennaGainDbi5Ghz): static
    {
        $this->antennaGainDbi5Ghz = $antennaGainDbi5Ghz;
        return $this;
    }

    public function getEthernet100Ports(): ?int
    {
        return $this->ethernet100Ports;
    }

    public function setEthernet100Ports(?int $ethernet100Ports): static
    {
        $this->ethernet100Ports = $ethernet100Ports;
        return $this;
    }

    public function getEthernet1000Ports(): ?int
    {
        return $this->ethernet1000Ports;
    }

    public function setEthernet1000Ports(?int $ethernet1000Ports): static
    {
        $this->ethernet1000Ports = $ethernet1000Ports;
        return $this;
    }

    public function getNumberOfEthernet25GPorts(): ?int
    {
        return $this->numberOfEthernet25GPorts;
    }

    public function setNumberOfEthernet25GPorts(?int $numberOfEthernet25GPorts): static
    {
        $this->numberOfEthernet25GPorts = $numberOfEthernet25GPorts;
        return $this;
    }

    public function getNumberOfUsbPorts(): ?int
    {
        return $this->numberOfUsbPorts;
    }

    public function setNumberOfUsbPorts(?int $numberOfUsbPorts): static
    {
        $this->numberOfUsbPorts = $numberOfUsbPorts;
        return $this;
    }

    public function getEthernetComboPorts(): ?string
    {
        return $this->ethernetComboPorts;
    }

    public function setEthernetComboPorts(?string $ethernetComboPorts): static
    {
        $this->ethernetComboPorts = $ethernetComboPorts;
        return $this;
    }

    public function getSfpPorts(): ?int
    {
        return $this->sfpPorts;
    }

    public function setSfpPorts(?int $sfpPorts): static
    {
        $this->sfpPorts = $sfpPorts;
        return $this;
    }

    public function getSfpPlusPorts(): ?int
    {
        return $this->sfpPlusPorts;
    }

    public function setSfpPlusPorts(?int $sfpPlusPorts): static
    {
        $this->sfpPlusPorts = $sfpPlusPorts;
        return $this;
    }

    public function getNumberOfHighSpeedEthernetPorts(): ?int
    {
        return $this->numberOfHighSpeedEthernetPorts;
    }

    public function setNumberOfHighSpeedEthernetPorts(?int $numberOfHighSpeedEthernetPorts): static
    {
        $this->numberOfHighSpeedEthernetPorts = $numberOfHighSpeedEthernetPorts;
        return $this;
    }

    public function getNumberOfSimSlots(): ?int
    {
        return $this->numberOfSimSlots;
    }

    public function setNumberOfSimSlots(?int $numberOfSimSlots): static
    {
        $this->numberOfSimSlots = $numberOfSimSlots;
        return $this;
    }

    public function getMemoryCards(): ?string
    {
        return $this->memoryCards;
    }

    public function setMemoryCards(?string $memoryCards): static
    {
        $this->memoryCards = $memoryCards;
        return $this;
    }

    public function getUsbSlotType(): ?string
    {
        return $this->usbSlotType;
    }

    public function setUsbSlotType(?string $usbSlotType): static
    {
        $this->usbSlotType = $usbSlotType;
        return $this;
    }

    public function getSuggestedPriceUsd(): ?float
    {
        return $this->suggestedPriceUsd;
    }

    public function setSuggestedPriceUsd(?float $suggestedPriceUsd): static
    {
        $this->suggestedPriceUsd = $suggestedPriceUsd;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
