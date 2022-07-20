<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220720124100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D15E4C1FD');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D9D86650F');
        $this->addSql('DROP INDEX IDX_773DE69D15E4C1FD ON car');
        $this->addSql('DROP INDEX IDX_773DE69D9D86650F ON car');
        $this->addSql('ALTER TABLE car ADD user_id INT NOT NULL, ADD fuel_id INT NOT NULL, DROP user_id_id, DROP fuel_id_id');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D97C79677 FOREIGN KEY (fuel_id) REFERENCES fuel (id)');
        $this->addSql('CREATE INDEX IDX_773DE69DA76ED395 ON car (user_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D97C79677 ON car (fuel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69DA76ED395');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D97C79677');
        $this->addSql('DROP INDEX IDX_773DE69DA76ED395 ON car');
        $this->addSql('DROP INDEX IDX_773DE69D97C79677 ON car');
        $this->addSql('ALTER TABLE car ADD user_id_id INT NOT NULL, ADD fuel_id_id INT NOT NULL, DROP user_id, DROP fuel_id');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D15E4C1FD FOREIGN KEY (fuel_id_id) REFERENCES fuel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_773DE69D15E4C1FD ON car (fuel_id_id)');
        $this->addSql('CREATE INDEX IDX_773DE69D9D86650F ON car (user_id_id)');
    }
}
