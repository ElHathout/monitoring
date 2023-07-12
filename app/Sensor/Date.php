<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Sensor;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class Date extends \App\AbstractSensor
{
    //put your code here
    public function report(array $records) : string
    {
        return "<p>Time drift: " . $this->delta(end($records)) . " seconds</p>";
    }

    public function status(array $records) : int
    {
        $delta = $this->delta(end($records));
        if ($delta == null) {
            return \App\Status::UNKNOWN;
        }

        if (abs($delta) > 10) {
            return \App\Status::WARNING;
        }

        return \App\Status::OK;
    }

    public function delta($record)
    {
        if (! isset($record["date"])) {
            return null;
        }

        return $record->date - $record->time;
    }
}
