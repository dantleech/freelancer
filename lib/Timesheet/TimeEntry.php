<?php

namespace DTL\Freelancer\Timesheet;

class TimeEntry
{
    private $start;
    private $end;
    private $comment;
    private $group;

    public function __construct(\DateTime $start, \DateTime $end, $comment, $group)
    {
        $this->start = $start;
        $this->end = $end;
        $this->comment = $comment;
        $this->group = $group;
    }

    public function getStart() 
    {
        return $this->start;
    }

    public function getEnd() 
    {
        return $this->end;
    }

    public function getInterval()
    {
        return $this->start->diff($this->end);
    }

    public function getComment() 
    {
        return $this->comment;
    }

    public function getGroup() 
    {
        return $this->group;
    }
}
