<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220820173821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address CHANGE city_id city_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D97C79677');
        $this->addSql('ALTER TABLE car CHANGE fuel_id fuel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D97C79677 FOREIGN KEY (fuel_id) REFERENCES fuel (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE messages CHANGE author_id author_id INT DEFAULT NULL, CHANGE receiver_id receiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B02737BC4DC6');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027386D8D01');
        $this->addSql('ALTER TABLE opinion CHANGE emitter_id emitter_id INT DEFAULT NULL, CHANGE receptor_id receptor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B02737BC4DC6 FOREIGN KEY (emitter_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027386D8D01 FOREIGN KEY (receptor_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BC3423909');
        $this->addSql('ALTER TABLE trip CHANGE driver_id driver_id INT DEFAULT NULL, CHANGE start_address_id start_address_id INT DEFAULT NULL, CHANGE destination_address_id destination_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BC3423909 FOREIGN KEY (driver_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F818BAC62AF');
        $this->addSql('ALTER TABLE address CHANGE city_id city_id INT NOT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE car DROP FOREIGN KEY FK_773DE69D97C79677');
        $this->addSql('ALTER TABLE car CHANGE fuel_id fuel_id INT NOT NULL');
        $this->addSql('ALTER TABLE car ADD CONSTRAINT FK_773DE69D97C79677 FOREIGN KEY (fuel_id) REFERENCES fuel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE messages CHANGE author_id author_id INT NOT NULL, CHANGE receiver_id receiver_id INT NOT NULL');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B02737BC4DC6');
        $this->addSql('ALTER TABLE opinion DROP FOREIGN KEY FK_AB02B027386D8D01');
        $this->addSql('ALTER TABLE opinion CHANGE emitter_id emitter_id INT NOT NULL, CHANGE receptor_id receptor_id INT NOT NULL');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B02737BC4DC6 FOREIGN KEY (emitter_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE opinion ADD CONSTRAINT FK_AB02B027386D8D01 FOREIGN KEY (receptor_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53BC3423909');
        $this->addSql('ALTER TABLE trip CHANGE driver_id driver_id INT NOT NULL, CHANGE start_address_id start_address_id INT NOT NULL, CHANGE destination_address_id destination_address_id INT NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BC3423909 FOREIGN KEY (driver_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP roles');
    }
}
