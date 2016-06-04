<?php

namespace DTL\Freelancer\Model\Helper;

class ClientHelper
{
    const CLIENTS_DIR = 'clients';

    private $baseDir;

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function getClientPath($code)
    {
        if (strlen($code) !== 6) {
            throw new \InvalidArgumentException(sprintf(
                'Client codes must be 6 characters long. Got "%s"',
                $code
            ));
        }

        return implode('/', [
            $this->baseDir,
            self::CLIENTS_DIR, 
            $code, 
            'client.yml'
        ]);
    }

    public function getClientDir($code)
    {
        return dirname($this->getClientPath($code));
    }
}
