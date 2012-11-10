<?php

require_once(dirname(__FILE__).'/iocontroller.class.php');

class LEDController extends IOController
{

  private $LEDPin = 2;

  function __construct($pin) {

    $this->LEDPin = $pin;
  }

  public function setHeartbeatTrigger()
  {
    $this->write(1);
    $this->write_string_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/trigger", 'heartbeat');
  }
  public function setNoTrigger()
  {
    $this->write(1);
    $this->write_string_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/trigger", 'none');
  }
  
  public function blink($delay_on=500, $delay_off=500)
  {
    $this->write(1);
    $this->write_string_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/trigger", 'timer');
    $this->write_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/delay_on", $delay_on);
    $this->write_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/delay_off", $delay_off);
  }
  public function write($value) {

    return $this->write_to_port("/sys/class/leds/beaglebone::usr".$this->LEDPin."/brightness",$value);
  }

}
