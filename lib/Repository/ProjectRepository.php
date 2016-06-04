<?php

namespace DTL\Freelancer\Repository;

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use DTL\Freelancer\Model\Client;
use DTL\Freelancer\Model\Project;
use DTL\Freelancer\Model\Helper\ProjectHelper;
use DTL\Freelancer\Timesheet\Timesheet;

class ProjectRepository
{
    private $normalizer;
    private $filesystem;
    private $helper;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->normalizer = new ObjectNormalizer();
        $this->helper = new ProjectHelper();
    }

    public function findProjectsForClient(Client $client)
    {
        $projectsDir = $this->helper->getProjectsDirForClient($client);

        if (!$this->filesystem->exists($projectsDir)) {
            throw new \InvalidArgumentException(sprintf(
                'Directory "%s" does not exist',
                $projectsDir
            ));
        }

        $finder = Finder::create()
            ->files()
            ->name('project.yml')
            ->depth('==2');

        $projects = [];
        foreach ($finder->in($projectsDir) as $project) {
            $projectName = sprintf('%s%s', basename(dirname(dirname($project->getPathname()))), basename(dirname($project->getPathname())));
            $projects[$projectName] = $this->findProjectForClient($client, $projectName);
        }

        ksort($projects);

        return $projects;
    }

    public function findProjectForClient(Client $client, $code)
    {
        $projectPath = $this->helper->getPathForClient($client, $code);

        if (!$this->filesystem->exists($projectPath)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find project data file "%s"', $projectPath
            ));
        }

        $projectData = Yaml::parse(file_get_contents($projectPath));
        $project = $this->normalizer->denormalize($projectData, Project::class);
        $project->code = $code;
        $project->path = realpath($projectPath);
        $project->client = $client;

        return $project;
    }

    public function getTimesheet(Project $project)
    {
        $path = $this->helper->getTimesheetPath($project);

        // install a template if the file does not exist.
        if (!file_exists($path)) {
            copy(__DIR__ . '/fixture/timesheet.template', $path);
        }

        return Timesheet::create($path);
    }
}
