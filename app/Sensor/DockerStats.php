<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of container stats in docker
 *
 * @author Elias El Hathout
 */
class DockerStats extends AbstractSensor
{

    public function report(array $records) : string
    {

        $record = end($records);
        if (! isset($record['DockerStats'])) {
            return "<p>No data available...</p>";
        }
        $containers = $this->parse($record['DockerStats']);
        $return = "<table class='table table-sm'>";
        $return .= "<tr>"
                . "<th>Id</th>"
                . "<th>Name</th>"
                . "<th>CPU %</th>"
                . "<th>Memory Usage</th>"
                . "<th>Memory Limit</th>"
                . "<th>Memory %</th>"
                . "<th>Network Input %</th>"
                . "<th>Network Output %</th>"
                . "<th>Block Input %</th>"
                . "<th>Block Output %</th>"
                . "<th>PID</th>"
                . "</tr>";
        foreach ($containers as $container) {
            $return .= "<tr>"
                    . "<td>" . $container['Id'] . "</td>"
                    . "<td>" . $container['Name'] . "</td>"
                    . "<td>" . $container['CPU'] . "</td>"
                    . "<td>" . $container['Mem_usage'] . "</td>"
                    . "<td>" . $container['Mem_limit'] . "</td>"
                    . "<td>" . $container['Mem'] . "</td>"
                    . "<td>" . $container['Net_input'] . "</td>"
                    . "<td>" . $container['Net_output'] . "</td>"
                    . "<td>" . $container['Block_input'] . "</td>"
                    . "<td>" . $container['Block_output'] . "</td>"
                    . "<td>" . $container['PID'] . "</td>"
                    . "</tr>";
        }
        $return .= "</table>";
        return $return;
    }

    public function status(array $records) : int
    {
        return \App\Status::OK;
    }

    /**
     * Parse the result of the docker stats command, skipping every virtual
     * @param string $string
     * @return 
     */
    public function parse(string $string)
    {
        if ($string == null) {
            return [];
        }
        $result = [];
        $lines = explode("\n", $string);
        for ($i = 1; $i < count($lines); $i++) {
            $line = preg_split('/\s+/', $lines[$i]);
            $container = [];
            $container['Id'] = $line[0];
            $container['Name'] = $line[1];
            $container['CPU'] = $line[2];
            $container['Mem_usage'] = $line[3];
            $container['Mem_limit'] = $line[5];
            $container['Mem'] = $line[6];
            $container['Net_input'] = $line[7];
            $container['Net_output'] = $line[9];
            $container['Block_input'] = $line[10];
            $container['Block_output'] = $line[12];
            $container['PID'] = $line[13];
            $result[$i-1] = $container;
        }

        return $result;
    }

}
