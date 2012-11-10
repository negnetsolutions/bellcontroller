<?php

class IOController
{

  private $handle;
  protected $DEALLOC_PIN = true;

  protected function write_string_to_port($path,$value) {

    if( ($handle = fopen($path, "r+") ) === false ) {
      echo "Cannot open direction handle.\n";
      exit;
    }

    fwrite($handle, $value);
    fclose($handle);

    return true;
  }
  protected function write_to_port($path,$value) {

    if( ($handle = fopen($path, "rb+") ) === false ) {
      echo "Cannot open direction handle.\n";
      exit;
    }

    fwrite($handle, $value);
    fclose($handle);

    return true;
  }
  public function allocPin() {

  }

  public function deallocPin() {

  }

  function __destruct() {
    if( $this->DEALLOC_PIN == true )
      $this->deallocPin();
  }

}
