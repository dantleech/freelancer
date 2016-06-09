<?php

namespace DTL\Freelancer\Model\Helper;

use DTL\Freelancer\Model\Client;
use DTL\Freelancer\Model\Project;

class ProjectHelper
{
    const PROJECT_DIR = 'projects';
    const PROJECT_CODE_PATTERN = '[0-9]{2}[A-Z0-9]{6}';

    public function getProjectsDirForClient($client)
    {
        return implode('/', [ dirname($client->path), self::PROJECT_DIR]);
    }

    public function getPathForClient(Client $client, $code)
    {
        self::validateProjectCode($code);
        $code = self::expandProjectCode($code);
        $projectPath = implode('/', [
            dirname($client->path),
            self::PROJECT_DIR,
            $code,
            'project.yml'
        ]);

        return $projectPath;
    }

    public function getTimesheetPath(Project $project)
    {
        return dirname($this->getPathForClient($project->client, $project->code)) . '/timesheet';
    }

    public function expandProjectCode($code)
    {
        return sprintf('%s/%s', substr($code, 0, 2), substr($code, 2));
    }

    public function validateProjectCode($code)
    {
        if (preg_match('{' . self::PROJECT_CODE_PATTERN . '}', $code)) {
            return;
        }

        throw new \InvalidArgumentException(sprintf(
            'Project code must match regex: "%s", got "%s"',
            self::PROJECT_CODE_PATTERN,
            $code
        ));
    }
}
