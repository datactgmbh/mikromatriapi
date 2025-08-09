<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250809184123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE products (
          id INT AUTO_INCREMENT NOT NULL,
          product_name VARCHAR(255) NOT NULL,
          product_code VARCHAR(100) NOT NULL,
          architecture VARCHAR(50) DEFAULT NULL,
          cpu VARCHAR(100) DEFAULT NULL,
          cpu_core_count INT DEFAULT NULL,
          cpu_nominal_frequency VARCHAR(100) DEFAULT NULL,
          dimensions VARCHAR(100) DEFAULT NULL,
          license_level INT DEFAULT NULL,
          operating_system VARCHAR(50) DEFAULT NULL,
          size_of_ram VARCHAR(50) DEFAULT NULL,
          storage_size VARCHAR(50) DEFAULT NULL,
          poe_in VARCHAR(50) DEFAULT NULL,
          poe_out VARCHAR(50) DEFAULT NULL,
          poe_out_ports VARCHAR(100) DEFAULT NULL,
          poe_in_input_voltage VARCHAR(50) DEFAULT NULL,
          number_of_dc_inputs INT DEFAULT NULL,
          dc_jack_input_voltage VARCHAR(50) DEFAULT NULL,
          max_power_consumption VARCHAR(50) DEFAULT NULL,
          wireless24_ghz_number_of_chains INT DEFAULT NULL,
          antenna_gain_dbi24_ghz VARCHAR(50) DEFAULT NULL,
          wireless5_ghz_number_of_chains INT DEFAULT NULL,
          antenna_gain_dbi5_ghz VARCHAR(50) DEFAULT NULL,
          ethernet100_ports INT DEFAULT NULL,
          ethernet1000_ports INT DEFAULT NULL,
          number_of_ethernet25_gports INT DEFAULT NULL,
          number_of_usb_ports INT DEFAULT NULL,
          ethernet_combo_ports VARCHAR(50) DEFAULT NULL,
          sfp_ports INT DEFAULT NULL,
          sfp_plus_ports INT DEFAULT NULL,
          number_of_high_speed_ethernet_ports INT DEFAULT NULL,
          number_of_sim_slots INT DEFAULT NULL,
          memory_cards VARCHAR(100) DEFAULT NULL,
          usb_slot_type VARCHAR(50) DEFAULT NULL,
          suggested_price_usd NUMERIC(8, 2) DEFAULT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          UNIQUE INDEX UNIQ_B3BA5A5AFAFD1239 (product_code),
          INDEX idx_product_code (product_code),
          INDEX idx_architecture (architecture),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products');
    }
}
