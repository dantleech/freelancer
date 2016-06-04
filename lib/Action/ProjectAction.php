<?php

namespace DTL\Freelancer\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Twig_Environment;
use DTL\Freelancer\Repository\ClientRepository;
use DTL\Freelancer\Repository\ProjectRepository;

class ProjectAction
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
        $projectCode = $request->getAttribute('route')->attributes['project'];
        $client = $this->clientRepository->findClient($clientCode);
        $project = $this->projectRepository->findProjectForClient($client, $projectCode);
        $timesheet = $this->projectRepository->getTimesheet($project);

        $response->getBody()->write(
            $this->twig->render(
                'project/index.html.twig',
                [
                    'client' => $client,
                    'project' => $project,
                    'timesheet' => $timesheet,
                ]
            )
        );
        return $response;
    }
}

