<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527230153 extends AbstractMigration {
    public function getDescription() : string {
        return '';
    }

    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE compilation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_AD5F8DF26DE44026 (description), FULLTEXT INDEX IDX_AD5F8DF2EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_AD5F8DF25E237E06 (name), FULLTEXT INDEX IDX_AD5F8DF2EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE folio (id INT AUTO_INCREMENT NOT NULL, item_id INT DEFAULT NULL, page_number INT NOT NULL, status VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, text LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, hocr LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9BEA0CC6126F525E (item_id), FULLTEXT INDEX folio_text_ft (text), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, scrapbook_id INT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1F1B251E7F0D587F (scrapbook_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_dc_element (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, uri VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, comment LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_C0F4D2B16DE44026 (description), FULLTEXT INDEX IDX_C0F4D2B1EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_C0F4D2B1841CB121 (uri), FULLTEXT INDEX IDX_C0F4D2B1EA750E8 (label), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_dc_value (id INT AUTO_INCREMENT NOT NULL, element_id INT DEFAULT NULL, data VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, FULLTEXT INDEX nines_dc_value_ft (data), INDEX IDX_879CABBA1F1F2A24 (element_id), INDEX nines_dc_value_entity (entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_audio (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, INDEX IDX_9D15F751E284468 (entity), FULLTEXT INDEX nines_media_audio_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_image (id INT AUTO_INCREMENT NOT NULL, thumb_path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, image_width INT NOT NULL, image_height INT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, INDEX IDX_4055C59BE284468 (entity), FULLTEXT INDEX nines_media_image_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_link (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(500) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, text VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3B5D85A3E284468 (entity), FULLTEXT INDEX nines_media_link_ft (url, text), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_media_pdf (id INT AUTO_INCREMENT NOT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mime_type VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, file_size INT NOT NULL, thumb_path VARCHAR(128) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, license LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, entity VARCHAR(120) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_9286B706E284468 (entity), FULLTEXT INDEX nines_media_pdf_ft (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE nines_user (id INT AUTO_INCREMENT NOT NULL, active TINYINT(1) NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, reset_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, reset_expiry DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', fullname VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, affiliation VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\', login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5BA994A1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE scrapbook (id INT AUTO_INCREMENT NOT NULL, compilation_id INT DEFAULT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, label VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_DA6FCDF9EA750E8 (label), FULLTEXT INDEX IDX_DA6FCDF96DE44026 (description), UNIQUE INDEX UNIQ_DA6FCDF95E237E06 (name), FULLTEXT INDEX IDX_DA6FCDF9EA750E86DE44026 (label, description), INDEX IDX_DA6FCDF9A5F8C840 (compilation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE compilation');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE folio');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE item');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_dc_element');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_dc_value');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_audio');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_image');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_link');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_media_pdf');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE nines_user');
        $this->abortIf(
            ! $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\\Doctrine\\DBAL\\Platforms\\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE scrapbook');
    }
}
