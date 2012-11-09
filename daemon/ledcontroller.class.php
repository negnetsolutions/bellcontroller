<?php

require_once(dirname(__FILE__).'/iocontroller.class.php');

class LEDController 
{

  private $LEDPin = 2;

  function __construct($pin) {

    $this->LEDPin = $pin;
  }

 public function write($value) {

    return $this->write_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/brightness",$value);
  }

}
