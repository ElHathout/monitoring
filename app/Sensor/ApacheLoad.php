<?php

namespace App\Sensor;

use \App\AbstractSensor;

/**
 * Description of Apache metrics
 *
 * @author Elias El Hathout
 */
class ApacheLoad extends AbstractSensor
{
    public function report(array $records) : string
    {

        $record = end($records);
        if (! isset($record['ApacheLoad'])) {
            return "<p>No data available...</p>";
        }
        $ApacheData = $this->parse($record['ApacheLoad']);

        $return = "Server Version: ". $ApacheData["version"] 
                . "\nServer Uptime: ". $ApacheData["uptime"]
                . "\nServer Load: ". $ApacheData["load"]
                . "\nTotal accesses: ". $ApacheData["accesses"]
                . "\nTotal traffic: ". $ApacheData["traffic"]
                . "\nTotal duration: ". $ApacheData["uptime"]
                . "\nCPU usage: ". $ApacheData["CPULoad"]
                . "\nRequests per seconds: ". $ApacheData["requests/s"]
                . "\nBytes per second: ". $ApacheData["B/second"]
                . "\nBytes per request: ". $ApacheData["B/request"]
                . "\nmilliseconds per request: ". $ApacheData["ms/Request"]
                ;
        return  "<p>" . nl2br($return) . "</p>";
    }

    public function status(array $records) : int
    {
        return \App\Status::OK;
    }

    /**
     * Parse the result of the apache server status page
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
            if (str_contains($lines[$i], 'Server Version')){
                $result["version"] = substr($lines[$i], 31, 6);
            }
            if (str_contains($lines[$i], 'Server uptime')){
                $result["uptime"] = substr($lines[$i], 20, 36);
            }
            if (str_contains($lines[$i], 'Server load')){
                $result["load"] = substr($lines[$i], 17, 13);
            }
            if (str_contains($lines[$i], 'Total accesses')){
                $result["accesses"] = substr($lines[$i], 21, 2);
                $result["traffic"] = substr($lines[$i], 41, 5);
                $result["duration"] = substr($lines[$i], 65, strlen($lines[$i])-2);
            }
            if (str_contains($lines[$i], 'CPU Usage')){
                $result["CPULoad"] = preg_split('/\s+/', explode("-", $lines[$i])[1])[1];
            }
            if (str_contains($lines[$i], 'requests/sec')){
                $splittedLine =  preg_split('/\s+/',  $lines[$i]);
                $result["requests/s"] = substr($splittedLine[0], 4, strlen($splittedLine[0])-4);
                $result["B/second"] = $splittedLine[3];
                $result["B/request"] = $splittedLine[6];
                $result["ms/Request"] = $splittedLine[9];
            }
        }

        return $result;
    }
}