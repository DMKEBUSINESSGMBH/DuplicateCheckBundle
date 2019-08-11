<?php

namespace DMK\DuplicateCheckBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class DMKDuplicateCheckBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries): void
    {
        /* Tables generation **/
        $this->createDmkDuplicateTable($schema);

        /* Foreign keys generation **/
        $this->addDmkDuplicateForeignKeys($schema);
    }

    /**
     * Create dmk_duplicate table.
     *
     * @param Schema $schema
     *
     * @return void
     */
    protected function createDmkDuplicateTable(Schema $schema)
    {
        $table = $schema->createTable('dmk_duplicate');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('class', 'string', ['length' => 255]);
        $table->addColumn('weight', 'smallint', []);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('object_id', 'integer', []);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addIndex(['class', 'object_id'], 'dmk_duplicate_idx', []);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id'], 'idx_b867c2a032c8a3de', []);
    }

    /**
     * Add dmk_duplicate foreign keys.
     *
     * @param Schema $schema
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function addDmkDuplicateForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('dmk_duplicate');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }
}
