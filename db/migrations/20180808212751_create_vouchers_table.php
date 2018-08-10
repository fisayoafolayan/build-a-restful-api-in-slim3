<?php
use Phinx\Migration\AbstractMigration;

class CreateVouchersTable extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        $voucher = $this->table('vouchers');
        $voucher->addColumn('code', 'string')
              ->addColumn('offer_id', 'integer', ['null' => true])
              ->addColumn('user_id', 'integer', ['null' => true])
              ->addColumn('is_used', 'integer',['default' => 0])
              ->addColumn('date_used', 'datetime', ['null' => true])
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('deleted_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
              ->addForeignKey('offer_id', 'offers', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'])
              ->addIndex(['code'], ['unique' => true]) 

              ->save();

              
    }
    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('voucher');
    }
}