<?php

namespace DTL\Freelancer\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use DTL\Freelancer\Repository\ClientRepository;
use DTL\Freelancer\Repository\ProjectRepository;

class ClientAction
{
    private $twig;
    private $clientRepository;
    private $projectRepository;

    public function __construct(
        Twig_Environment $twig,
        ClientRepository $clientRepository,
        ProjectRepository $projectRepository
    )
    {
        $this->twig = $twig;
        $this->clientRepository = $clientRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $clientCode = $request->getAttribute('route')->attributes['client'];
        $client = $this->clientRepository->findClient($clientCode);
        $projects = $this->projectRepository->findProjectsForClient($client);

        $response->getBody()->write(
            $this->twig->render(
                'client/index.html.twig',
                [
                    'client' => $client,
                    'projects' => $projects,
                ]
            )
        );
        return $response;
    }
}
