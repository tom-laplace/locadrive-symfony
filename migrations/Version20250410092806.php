<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250410092806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE insurance (id SERIAL NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "order" (id SERIAL NOT NULL, customer_id INT NOT NULL, insurance_id INT DEFAULT NULL, payment_method_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, status VARCHAR(50) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F52993989395C3F3 ON "order" (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F5299398D1E63CD1 ON "order" (insurance_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F52993985AA1164F ON "order" (payment_method_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_F52993984C3A3BB ON "order" (payment_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE order_item (id SERIAL NOT NULL, order_ref_id INT NOT NULL, vehicle_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52EA1F09E238517C ON order_item (order_ref_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_52EA1F09545317D1 ON order_item (vehicle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment (id SERIAL NOT NULL, amount DOUBLE PRECISION NOT NULL, payment_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, transaction_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payment_method (id SERIAL NOT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, license_obtainment_date DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vehicle (id SERIAL NOT NULL, brand VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, daily_rate DOUBLE PRECISION NOT NULL, is_available BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" ADD CONSTRAINT FK_F5299398D1E63CD1 FOREIGN KEY (insurance_id) REFERENCES insurance (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" ADD CONSTRAINT FK_F52993985AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" ADD CONSTRAINT FK_F52993984C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09E238517C FOREIGN KEY (order_ref_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F09545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" DROP CONSTRAINT FK_F52993989395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" DROP CONSTRAINT FK_F5299398D1E63CD1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" DROP CONSTRAINT FK_F52993985AA1164F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "order" DROP CONSTRAINT FK_F52993984C3A3BB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09E238517C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE order_item DROP CONSTRAINT FK_52EA1F09545317D1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE insurance
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "order"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payment_method
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vehicle
        SQL);
    }
}
