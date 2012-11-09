<?php

require_once(dirname(__FILE__).'/screeniocontroller.class.php');
require_once(dirname(__FILE__).'/ledcontroller.class.php');
require_once(dirname(__FILE__).'/gpiocontroller.class.php');

define("STATUS_FILE", "/Users/andy/Sites/bell/daemon/status");
define("ON", 1);
define("OFF", 0);

class bellcontroll {

  private $alert_string = '(800)1200(800)';
  private $stages = array();
  private $io = array();
  
  function __construct($args)
  {
    if( isset($args[1]) ) {
      $this->alert_string = $args[1];
    }

    $this->parseStages();
  }
  public function defineIO($io)
  {
    $this->io = $io;
  }
  private function parseStages()
  {
    
    if( preg_match_all('/\(?([0-9]+)\)?/', $this->alert_string, $matches) === false )
      die("Could parse alert string!\n");

    $stages = array();
    foreach( $matches[0] as $key => $match ) {
      $type = OFF;
      
      if( $match[0] == '(' ) {
        $type = ON;
      }

      $stages[] = array(
        'type' => $type,
        'delay' => $matches[1][$key]
      );
    
    }

    $this->stages = $stages;
    
  }
  private function writeStatus($status)
  {
    if( !file_put_contents(STATUS_FILE, $status) ) {
      die("Could not write to status file!");
    }
  }
  public function start()
  {
    if( count($this->io) == 0 ) {
      echo "No IO Set!";
      return false;
    }
    
    $this->writeStatus('Ringing...');
    $this->sendIO();
    $this->writeStatus('Ready.');

    return true;
  }
  private function writeIO($status) {
    foreach($this->io as $io ) {
      $io->write($status);
    }
  }
  private function delay($delay)
  {
    usleep($delay* 1000);
    // echo ($delay*1000)."\n";
    // 200000
    // 800000
    // 200
  }
  private function sendIO()
  {
    foreach($this->stages as $stage) {

      $this->writeIO($stage['type']);
      $this->delay($stage['delay']);
    }
  }


}
