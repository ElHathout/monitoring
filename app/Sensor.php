<?php

namespace App;

interface Sensor
{
    public function status(array $records) : int;
    public function report(array $records) : string;

    /**
     * Get the name of this sensor (e.g meminfo, cpuload, heartbeat)
     * @return string
     */
    public function name() : string;
}
