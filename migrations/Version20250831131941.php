<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250831131941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Notification entity with subject, body, recipient, and sentAt fields';
    }


    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification (id SERIAL NOT NULL, subject VARCHAR(255) NOT NULL, body TEXT NOT NULL, recipient VARCHAR(255) NOT NULL, sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notification.sent_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification');
    }
}
