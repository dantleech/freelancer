<?php

namespace DTL\Freelancer\Timesheet;

class Timesheet implements \IteratorAggregate
{
    private $entries;

    /**
     * @param TimesheetEntry[] $entries
     */
    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    public static function create($timesheetFile)
    {
        if (!file_exists($timesheetFile)) {
            throw new \RuntimeException(sprintf(
                'Timesheet file "%s" does not exist.',
                $timesheetFile
            ));
        }

        $handle = fopen($timesheetFile, 'r');
        $date = null;
        $lineNo = -1;
        $entries = [];

        while (false !== $line = fgets($handle)) {
            $lineNo++;

            // skip comments
            if (substr($line, 0, 1) === '#') { 
                continue;
            }

            if (preg_match('{^[0-9]{4}-[0-9]{2}-[0-9]{2}$}', trim($line))) {
                $date = new \DateTime($line);
                $line = fgets($handle);

                if (null === $line) {
                    break;
                }

                $lineNo++;
            }

            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if (null === $date) {
                throw new \RuntimeException(sprintf(
                    'Timesheet did not begin with a date of form YYY-MM-DD, got "%s"',
                    $line
                ));
            }

            $match = preg_match(
                '{(?<start>[0-9]{2}:[0-9]{2}) (?<end>[0-9]{2}:[0-9]{2}) ?(?<comment>.*?)?#?(?<group>#\w+)?$}',
                $line,
                $matches
            );

            if (!$match) {
                throw new \RuntimeException(sprintf(
                    'Invalid timesheet entry "%s" in file "%s" line %s',
                    $line, $timesheetFile, $lineNo
                ));
            }

            $start = new \DateTime($date->format('Y-m-d') . 'T' . $matches['start'] . ':00Z');
            $end = new \DateTime($date->format('Y-m-d') . 'T' . $matches['end'] . ':00Z');

            if ($end < $start) {
                $end->add(\DateInterval::createFromDateString('+1 day'));
            }

            $entry = new TimeEntry(
                $start,
                $end,
                $matches['comment'],
                isset($matches['group']) ? $matches['group'] : null
            );

            $entries[] = $entry;
        }

        $entries = array_reverse($entries);

        return new Timesheet($entries);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->entries);
    }

    public function getTotalTime()
    {
        $reference = new \DateTime();
        $current = new \DateTime();
        foreach ($this->entries as $entry) {
            $current->add($entry->getInterval());
        }

        return $reference->diff($current);
    }
}
