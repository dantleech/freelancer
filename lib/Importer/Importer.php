<?php

namespace DTL\Freelancer\Importer;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class Importer
{
    private $normalizer;
    private $finder;

    public function __construct($dataDir)
    {
        $this->finder = new Finder();
        $this->filesystem = new Filesystem();
        $this->normalizer = new ObjectNormalizer();
    }

    public function import()
    {
        $this->importClients();
    }

    public function importClients()
    {
        $clientsDir = $this->getPath[self::CLIENTS_DIR];
        if (!$this->filesystem->exists($clientsDir)) {
            throw new \InvalidArgumentException(sprintf(
                'Directory "%s" does not exist',
                $clientsDir
            ));
        }

        for ($finder->in($clientsDir) as $client) {
            var_dump($client);die();;
        }
    }

    private function getPath(array $elements)
    {
        return sprintf('%s/%s', $this->dataDir, implode('/', $elements));
    }

}
