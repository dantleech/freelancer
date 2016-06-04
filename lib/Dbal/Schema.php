<?php

namespace DTL\Freelancer\Dbal;

use Doctrine\DBAL\Schema\Schema as BaseSchema;

class Schema extends BaseSchema
{
    public function __construct()
    {
        parent::__construct();
        $this->createClient();
    }

    private function createClient()
    {
        $table = $this->createTable('run');
        $table->addColumn('code', 'string', ['autoincrement' => false]);
        $table->addColumn('rate', 'integer');
        $table->addColumn('currency', 'string');
    }
}
