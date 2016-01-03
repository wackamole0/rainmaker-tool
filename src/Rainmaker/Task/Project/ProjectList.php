<?php

namespace Rainmaker\Task\Project;

use Rainmaker\Task\Task;

/**
 * Generates a view of all the Rainmaker project and branch containers.
 *
 * @package Rainmaker\Task
 */
class ProjectList extends Task
{

    protected $list = '';

    public function performTask()
    {
        $columnWidthId = 30;
        $columnWidthStatus = 15;
        $containerRepo = $this->getEntityManager()->getRepository('Rainmaker:Container');

        $list = array();
        $list[] = '+-' . str_repeat('-', $columnWidthId) . '-+-' . str_repeat('-', $columnWidthStatus) . '-+';
        $list[] = '| ' . str_pad('ID', $columnWidthId, ' ') . ' | ' . str_pad('Status', $columnWidthStatus, ' ') . ' |';
        $list[] = '+-' . str_repeat('-', $columnWidthId) . '-+-' . str_repeat('-', $columnWidthStatus) . '-+';
        foreach ($containerRepo->getProjectParentContainers() as $project) {
            $list[] = '| ' . str_pad($project->getName(), $columnWidthId, ' ') . ' | ' . str_pad($project->getStatusText(), $columnWidthStatus, ' ') . ' |';
            foreach ($containerRepo->getProjectBranchContainers($project) as $branch) {
                $list[] = '| ' . str_pad(' \_ ' . $branch->getName(), $columnWidthId, ' ') . ' | ' . str_pad($branch->getStatusText(), $columnWidthStatus, ' ') . ' |';
            }
        }
        $list[] = '+-' . str_repeat('-', $columnWidthId) . '-+-' . str_repeat('-', $columnWidthStatus) . '-+';

        $this->list = implode("\n", $list);
    }

    public function getList()
    {
        return $this->list;
    }

}
