<?php

namespace App\Sensor;

/**
 * Description of Point
 *
 * @author tibo
 */

class Point
{
    public $t = 0;
    public $y = 0;

    public function __construct($t, $y)
    {
        $this->t = $t;
        $this->y = $y;
    }
}
