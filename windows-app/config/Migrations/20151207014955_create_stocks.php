<?php
use Migrations\AbstractMigration;

class CreateStocks extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('stocks');
        $table->addColumn('device_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ])->addForeignKey('device_id', 'devices', 'id', array('delete'=>'CASCADE', 'update'=>'NO_ACTION'));


        $table->addColumn('minimum', 'float', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('maximum', 'float', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('tick_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->create();
    }
}


