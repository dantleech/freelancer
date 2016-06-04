<?php

namespace DTL\Freelancer\Tests\Unit\Timesheet;

use DTL\Freelancer\Timesheet\Timesheet;

class TimesheetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * It should parse the timesheet file.
     */
    public function testParseTimesheet()
    {
        $timesheet = Timesheet::create(__DIR__ . '/fixtures/timesheet1');
        $entries = iterator_to_array($timesheet);

        $this->assertCount(4, $entries);
        $this->assertEquals('14:00', $entries[0]->getStart()->format('H:i'));
        $this->assertEquals('17:00', $entries[0]->getEnd()->format('H:i'));
        $this->assertEquals('8h00m', $timesheet->getTotalTime()->format('%hh%Im'));
    }
}
