<?php

require_once(dirname(__FILE__).'/iocontroller.class.php');

class GPIOController extends IOController
{
  private $GPIOPin = 0;

  function __construct($pin, $deallocate_when_done=false) {

    $this->GPIOPin = $pin;
    $this->DEALLOC_PIN = $deallocate_when_done;
    $this->allocPin();
  
  }

  public function allocPin() {

    if( ($handle = fopen("/sys/class/gpio/export", "ab") ) === false ) {
      echo "Cannot export GPIO pin.\n";
      exit;
    }

    fwrite($handle, $this->GPIOPin);
    fclose($handle);

    
    if( ($handle = fopen("/sys/class/gpio/gpio".$this->GPIOPin."/direction", "rb+") ) === false ) {
      echo "Cannot open direction handle.\n";
      exit;
    }

    fwrite($handle, "out");
    fclose($handle);

    return true;

  }
  
  public function write($value) {

    return $this->write_to_port("/sys/class/gpio/gpio".$this->GPIOPin."/value",$value);
  }

  public function deallocPin() {

    if( ($handle = fopen("/sys/class/gpio/unexport", "ab") ) === false ) {
      echo "Cannot export GPIO pin.\n";
      exit;
    }

    fwrite($handle, $this->GPIOPin);
    fclose($handle);

  }

  

}
