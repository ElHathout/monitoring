<?php

namespace App\Sensor;

/**
 * Description of Ssacli
 *
 * @author tibo
 */
class Perccli extends \App\AbstractSensor
{
    const REGEXP = "/(\d+:\d+)\s+\d+\s+(\w+)\s+\d+\s+(.*(GB|TB))\s+\w+\s+(\w+)/";

    public function report(array $records) : string
    {
        $record = end($records);
        if (! isset($record['perccli'])) {
            return "<p>No data available...</p>";
        }

        $drives = $this->parse($record->perccli);
        $return = "<table class='table table-sm'>"
                . "<tr>"
                . "<th>Slot</th>"
                . "<th>Type</th>"
                . "<th>Size</th>"
                . "<th>Status</th>"
                . "</tr>";
        foreach ($drives as $disk) {
            $return .= "<tr>"
                    . "<td>" . $disk->slot . "</td>"
                    . "<td>" . $disk->type . "</td>"
                    . "<td>" . $disk->size . "</td>"
                    . "<td>" . $disk->status . "</td>"
                    . "</tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status(array $records) : int
    {
        $record = end($records);
        if (! isset($record['perccli'])) {
            return \App\Status::UNKNOWN;
        }

        $drives = $this->parse($record->ssacli);
        foreach ($drives as $disk) {
            if ($disk->status != "Onln") {
                return \App\Status::WARNING;
            }
        }

        return \App\Status::OK;
    }

    /**
     *
     * @param string $string
     * @return array \App\Sensor\PerccliDrive
     */
    public function parse($string)
    {
        $values = [];
        preg_match_all(self::REGEXP, $string, $values);

        $drives = [];
        $count = count($values[1]);
        for ($i = 0; $i < $count; $i++) {
            $drive = new PerccliDrive();
            $drive->slot = $values[1][$i];
            $drive->type = $values[5][$i];
            $drive->size = $values[3][$i];
            $drive->status = $values[2][$i];
            $drives[] = $drive;
        }
        return $drives;
    }
}
