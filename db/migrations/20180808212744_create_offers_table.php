<?php
use Phinx\Migration\AbstractMigration;

class CreateOffersTable extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        $offers = $this->table('offers');
        $offers->addColumn('name', 'string')
              ->addColumn('discount', 'integer')
              ->addColumn('expires_at', 'datetime')
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('deleted_at', 'datetime',['null' => true])
              ->save();
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('offers');
    }
}