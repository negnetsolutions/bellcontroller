#!/Applications/MAMP/bin/php/php5.4.4/bin/php
<?php

require_once(dirname(__FILE__).'/controller.class.php');

$bell = new bellcontroll($argv);

$bell->defineIO(array(
  new SCREENIOController(),
  // new GPIOController(60),
  // new LEDController(2)
));

return $bell->start();

