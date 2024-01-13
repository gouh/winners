<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240113113559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Generate winner table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE winner (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(100) NOT NULL, 
            position SMALLINT NOT NULL, 
            PRIMARY KEY(id),
            UNIQUE KEY UNIQ_POSITION (position)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE winner');
    }
}
