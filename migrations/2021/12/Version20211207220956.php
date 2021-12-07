<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207220956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audio (id INT AUTO_INCREMENT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, license LONGTEXT DEFAULT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) NOT NULL, path VARCHAR(128) NOT NULL, mime_type VARCHAR(64) NOT NULL, file_size INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compilation (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, label VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_AD5F8DF2EA750E8 (label), FULLTEXT INDEX IDX_AD5F8DF26DE44026 (description), FULLTEXT INDEX IDX_AD5F8DF2EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_AD5F8DF25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dc_value (id INT AUTO_INCREMENT NOT NULL, element_id INT DEFAULT NULL, data LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(128) NOT NULL, INDEX IDX_AD50325B1F1F2A24 (element_id), FULLTEXT INDEX IDX_AD50325BADF3F363 (data), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE element (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, label VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, uri VARCHAR(190) NOT NULL, comment LONGTEXT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', FULLTEXT INDEX IDX_41405E39EA750E8 (label), FULLTEXT INDEX IDX_41405E396DE44026 (description), FULLTEXT INDEX IDX_41405E39EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_41405E39841CB121 (uri), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE folio (id INT AUTO_INCREMENT NOT NULL, item_id INT DEFAULT NULL, page_number INT NOT NULL, status VARCHAR(10) NOT NULL, text LONGTEXT DEFAULT NULL, hocr LONGTEXT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9BEA0CC6126F525E (item_id), FULLTEXT INDEX folio_text_ft (text), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, thumb_path VARCHAR(128) NOT NULL, image_width INT NOT NULL, image_height INT NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, license LONGTEXT DEFAULT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) NOT NULL, path VARCHAR(128) NOT NULL, mime_type VARCHAR(64) NOT NULL, file_size INT NOT NULL, FULLTEXT INDEX IDX_C53D045F545615306DE44026 (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, scrapbook_id INT DEFAULT NULL, public TINYINT(1) NOT NULL, original_name VARCHAR(128) NOT NULL, path VARCHAR(128) NOT NULL, mime_type VARCHAR(64) NOT NULL, file_size INT NOT NULL, thumb_path VARCHAR(128) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, license LONGTEXT DEFAULT NULL, INDEX IDX_1F1B251E7F0D587F (scrapbook_id), FULLTEXT INDEX IDX_1F1B251E545615306DE44026 (original_name, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE link (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(500) NOT NULL, text VARCHAR(200) DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', entity VARCHAR(128) NOT NULL, FULLTEXT INDEX IDX_36AC99F1F47645AE3B8BA7C7 (url, text), INDEX IDX_36AC99F1E284468 (entity), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nines_user (id INT AUTO_INCREMENT NOT NULL, active TINYINT(1) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_expiry DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', fullname VARCHAR(64) NOT NULL, affiliation VARCHAR(64) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5BA994A1E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scrapbook (id INT AUTO_INCREMENT NOT NULL, compilation_id INT DEFAULT NULL, name VARCHAR(200) NOT NULL, label VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DA6FCDF9A5F8C840 (compilation_id), FULLTEXT INDEX IDX_DA6FCDF9EA750E8 (label), FULLTEXT INDEX IDX_DA6FCDF96DE44026 (description), FULLTEXT INDEX IDX_DA6FCDF9EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_DA6FCDF95E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dc_value ADD CONSTRAINT FK_AD50325B1F1F2A24 FOREIGN KEY (element_id) REFERENCES element (id)');
        $this->addSql('ALTER TABLE folio ADD CONSTRAINT FK_9BEA0CC6126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E7F0D587F FOREIGN KEY (scrapbook_id) REFERENCES scrapbook (id)');
        $this->addSql('ALTER TABLE scrapbook ADD CONSTRAINT FK_DA6FCDF9A5F8C840 FOREIGN KEY (compilation_id) REFERENCES compilation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scrapbook DROP FOREIGN KEY FK_DA6FCDF9A5F8C840');
        $this->addSql('ALTER TABLE dc_value DROP FOREIGN KEY FK_AD50325B1F1F2A24');
        $this->addSql('ALTER TABLE folio DROP FOREIGN KEY FK_9BEA0CC6126F525E');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E7F0D587F');
        $this->addSql('DROP TABLE audio');
        $this->addSql('DROP TABLE compilation');
        $this->addSql('DROP TABLE dc_value');
        $this->addSql('DROP TABLE element');
        $this->addSql('DROP TABLE folio');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE link');
        $this->addSql('DROP TABLE nines_user');
        $this->addSql('DROP TABLE scrapbook');
    }
}
