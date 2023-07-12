<?php

namespace App\Sensor;

/**
 * Represents a single physical drive connected to a Dell RAID controller.
 *
 * @author tibo
 */
class PerccliDrive
{
    public $slot;
    public $status;
    public $size;
    public $type;
}
