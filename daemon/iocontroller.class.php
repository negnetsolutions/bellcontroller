<?php

class IOController
{

  private $handle;

  private function write_to_port($path,$value) {

    if( ($handle = fopen($path, "rb+") ) === false ) {
      echo "Cannot open direction handle.\n";
      exit;
    }

    fwrite($handle, $value);
    fclose($handle);

    return true;
  }


}
