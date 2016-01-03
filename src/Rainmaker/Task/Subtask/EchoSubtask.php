<?php

namespace Rainmaker\Task\Subtask;

class EchoSubtask extends Subtask
{

    protected $echoStr = 'Echo';

    public function getEcho()
    {
        return $this->echoStr;
    }

    public function setEcho($echo)
    {
        $this->echo = $echo;
    }

    public function performSubtask()
    {
        $this->output->writeln($this->echoStr);
    }

}