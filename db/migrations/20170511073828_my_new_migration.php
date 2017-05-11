<?php

use Phinx\Migration\AbstractMigration;

class MyNewMigration extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('history');
        $table->addColumn('currency_in', 'string')
            ->addColumn('currency_out', 'string')
            ->addColumn('amount', 'float')
            ->addColumn('result', 'float')
            ->create();

    }

    public function down()
    {

    }
}
