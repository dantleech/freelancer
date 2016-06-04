<?php

namespace DTL\Freelancer\Repository;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use DTL\Freelancer\Model\Client;
use DTL\Freelancer\Model\Helper\ClientHelper;

class ClientRepository
{
    private $normalizer;
    private $dataDir;
    private $filesystem;

    public function __construct($dataDir)
    {
        $this->helper = new ClientHelper($dataDir);
        $this->filesystem = new Filesystem();
        $this->normalizer = new ObjectNormalizer();
    }

    public function findClients()
    {
        $clientsDir = $this->getPath([self::CLIENTS_DIR]);
        if (!$this->filesystem->exists($clientsDir)) {
            throw new \InvalidArgumentException(sprintf(
                'Directory "%s" does not exist',
                $clientsDir
            ));
        }

        $finder = Finder::create()
            ->directories()
            ->depth(0);

        $clients = [];
        foreach ($finder->in($clientsDir) as $client) {
            $clientName = $client->getFilename();
            $clients[$clientName] = $this->findClient($clientName);
        }

        ksort($clients);

        return $clients;
    }

    public function findClient($code)
    {
        $clientDataPath = $this->helper->getClientPath($code);

        if (!$this->filesystem->exists($clientDataPath)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find client data file "%s"', $clientDataPath
            ));
        }

        $clientData = Yaml::parse(file_get_contents($clientDataPath));
        $client = $this->normalizer->denormalize($clientData, Client::class);
        $client->code = $code;
        $client->path = realpath($clientDataPath);

        return $client;
    }

    private function getPath(array $elements)
    {
        return sprintf('%s/%s', $this->dataDir, implode('/', $elements));
    }
}
