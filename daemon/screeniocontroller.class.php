<?php

require_once(dirname(__FILE__).'/iocontroller.class.php');

class SCREENIOController 
{
 public function write($value) {

   echo "Wrote: ".$value."\n";
   error_log( "Wrote: ".$value );
  }

}
