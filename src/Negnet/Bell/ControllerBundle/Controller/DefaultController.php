<?php

namespace Negnet\Bell\ControllerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Negnet\Bell\ControllerBundle\Entity\Token;
use Negnet\Bell\ControllerBundle\Entity\Mode;
// use Negnet\Bell\ControllerBundle\Entity\Action;
use Negnet\Bell\ControllerBundle\Entity\Alert;


define("CRONTAB_PRE_FILE", "/Users/andy/Sites/bell/daemon/crontab_pre");
define("CRONTAB_FILE", "/Users/andy/Sites/bell/daemon/crontab");
define("STATUS_FILE", "/Users/andy/Sites/bell/daemon/status");
define("RINGER", "/Users/andy/Sites/bell/daemon/ringer.php");
define("RINGER_USER", "andy");

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    private function getStatus()
    {
      if( ($status_line = file_get_contents(STATUS_FILE)) === false ) {
        return "Error";
      }

      return trim($status_line);
    }
    /**
     * @Route("/api/get_status", defaults={"_format"="json"})
     */
    public function getStatusAction()
    {
      
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }

      $data = array();
      $data['modes'] = $this->getModes();
      $data['status'] = $this->getStatus();
      return $this->getResponse($data);
    }

    /**
     * @Route("/api/get_schedule",  defaults={"_format"="json"})
     */
    public function getscheduleAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }

      if( ($schedule = file_get_contents(CRONTAB_PRE_FILE)) === false ) {
        return $this->getResponse(null, 'Could not read file: '.CRONTAB_PRE_FILE);
      }
      $data = array('status'=>$this->getStatus(),'schedule'=>$schedule,'alerts'=>$this->getAlerts());

      return $this->getResponse($data);
    }

    /**
     * @Route("/api/set_schedule", defaults={"_format"="json"})
     * @Method({"GET", "POST"})
     */
    public function setscheduleAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }
      
      $content = $this->getRequest()->getContent();

      if(empty($content)) {
        return $this->getResponse(null, 'No Data');
      }

      $params = json_decode($content, true);

      if(!$params) {
        return $this->getResponse(null, 'Data Corruption Error. Not Saving.');
      }

      if( !$params['data']['schedule'] ) {
        return $this->getResponse(null, 'Schedule Missing!');
      }
      
      //get a list of the valid alerts
      $alerts = array();
      foreach( $this->getAlerts() as $alert ) {
        $alerts[] = $alert['name'];
      }

      //check syntax
      foreach(preg_split("/((\r?\n)|(\r\n?))/", $params['data']['schedule']) as $number => $line){

        if( substr($line, 0, 1) == '#' ) {
          continue;
        }

        if( trim($line) == "" ) {
          continue;
        }

        if( !preg_match('/([0-9\/\*,]+\s){5}[a-z_]+$/', $line) ) {
          $data['syntax_error'][] = $number+1;
          continue;
        }
        $parts = explode(" ", $line);

        if( !isset($parts[5]) ) {
          $data['syntax_error'][] = $number+1;
          continue;
        }

        if( array_search(trim($parts[5]), $alerts) === false ){
          $data['syntax_error'][] = $number+1;
          continue;
        }

      }

      if( isset($data['syntax_error']) ) {
        $data['status'] = "Syntax Error(s).";
      }
      else {
        
        if( file_put_contents(CRONTAB_PRE_FILE,$params['data']['schedule'] ) === false ) {
          return $this->getResponse(null, 'Could not write to '.CRONTAB_PRE_FILE);
        }

        if( $this->writeCrontab($params['data']['schedule']) === false ) {
          return $this->getResponse(null, 'Could not save crontab!');
        }

        $data['status'] = "Schedule Updated.";
      }

      return $this->getResponse($data, false);
    }
    private function writeCrontab($data)
    {
      $alerts = $this->getAlerts();
      $alert_names = array();
      foreach( $alerts as $alert ) {
        $alert_names[] = $alert['name'];
      }

      $crontab = '';
      foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $number => $line){

        if( substr($line, 0, 1) == '#' ) {
          $crontab .= $line."\n";
          continue;
        }

        if( trim($line) == "" ) {
          $crontab .= $line."\n";
          continue;
        }

        $parts = explode(" ", $line);

        if( !isset($parts[5]) ) {
          return false;
        }

        if( ($alert_id = array_search(trim($parts[5]), $alert_names)) === false ){
          return false;
        }

        $crontab .= str_replace($parts[5], RINGER_USER . ' '. $this->getRinger($alerts[$alert_id]['beepcode']), $line)." /dev/null 2>&1\n";
      }
      
      if( file_put_contents(CRONTAB_FILE,$crontab ) === false ) {
        return false;
      }

      return true;
    }
    private function getModes()
    {
      $modes = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Mode')
        ->findAll()
      ;

      $modearray = array();
      foreach($modes as $mode) {
        $modearray[] = array(
          'name' => $mode->getName(),
          'alert' => (($alert=$this->getDoctrine()->getRepository('NegnetBellControllerBundle:Alert')->findOneById($mode->getAlert())) !== false) ? $alert->getName() : 'Invalid Alert!',
          'cycles' => $mode->getCycles(),
          'delay' => $mode->getDelay(),
          'status' => false
        );
      }

      return $modearray;
    }
    /**
     * @Route("/api/get_modes", defaults={"_format"="json"})
     */
    public function getmodesAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }

      $data = array(
        'modes' => $this->getModes()
      );
      return $this->getResponse($data);
    }
    /**
     * @Route("/api/set_modes", defaults={"_format"="json"})
     */
    public function setmodesAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }
      
      $content = $this->getRequest()->getContent();

      if(empty($content)) {
        return $this->getResponse(null, 'No Data');
      }

      $params = json_decode($content, true);

      if(!$params) {
        return $this->getResponse(null, 'Data Corruption Error. Not Saving.');
      }

      //delete old actions
      $em = $this->getDoctrine()->getEntityManager();
      
      $modes = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Mode')
        ->findAll()
      ;

      foreach($modes as $action) {
        $em->remove($action);
      }
      $em->flush();

      $modes = 0;
      foreach($params['data'] as $requested_modes) {

        $action = new Mode();
        $action->setName($requested_modes['name']);

        //find alert id
        if(($alert=$this->getDoctrine()->getRepository('NegnetBellControllerBundle:Alert')->findOneByName($requested_modes['alert'])) !== false) {
          $action->setAlert($alert->getId());
        }
        else {
          $action->setAlert(0);
        }

        $action->setCycles($requested_modes['cycles']);
        $action->setDelay($requested_modes['delay']);

        $modes++;
        $em->persist($action);
        
      }

      $em->flush();
      
      return $this->getResponse("Received $modes mode(s)",null);
    }
    private function getAlerts()
    {
      $modes = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Alert')
        ->findAll()
        ;
      
      $data = array();
      foreach($modes as $mode) {
        $data[] = array(
          'name' => $mode->getName(),
          'beepcode' => $mode->getBeepcode()
        );
        // echo $mode->getName()."\n";
      }
      
      return $data;
    }
    /**
     * @Route("/api/get_alerts", defaults={"_format"="json"})
     */
    public function getalertsAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }

      $modes = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Alert')
        ->findAll()
        ;

      $data = array();
      foreach($modes as $mode) {
        $data[] = array(
          'name' => $mode->getName(),
          'beepcode' => $mode->getBeepcode()
        );
      }

      return $this->getResponse(array('alerts'=>$data)); 
    }
    private function getRinger($beepcode)
    {
      return RINGER.' "'.$beepcode.'"';
    }
    private function ring($beepcode)
    {
      return exec( $this->getRinger($beepcode) );
    }
    /**
     * @Route("/api/ring", defaults={"_format"="json"})
     * @Method({"GET", "POST"})
     */
    public function ringAction()
    {
      if(($error=$this->validateKey($this->getRequest())) !== true) {
        return $this->getResponse(null, $error);
      }
      
      $content = $this->getRequest()->getContent();

      if(empty($content)) {
        return $this->getResponse(null, 'No Data');
      }

      $params = json_decode($content, true);

      if($params == false) {
        return $this->getResponse(null, "Could not read params");
      }
      
      $modes = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Alert')
        ->findAll()
        ;

      if(!isset($params['data']['alert'])) {
        return $this->getResponse(null, "alert not set");
      }

      //find the ringer
      $alert = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Alert')
        ->findOneByName($params['data']['alert'])
        ;
      
      if(!$alert) {
        return $this->getResponse(null, "Invalid alert!");
      }

      $this->ring($alert->getBeepcode());

      $data = array();
      $data['modes'] = $this->getModes();
      $data['status'] = $this->getStatus();
      
      return $this->getResponse($data);
    }

    private function validateKey($request)
    {
      if(!$request->headers->has('X-Server-Token')){
        return 'Server Token not set';
      }

      $valid_token = $this->getDoctrine()
        ->getRepository('NegnetBellControllerBundle:Token')
        ->findOneByToken($request->headers->get('X-Server-Token'))
      ;

      if(!$valid_token){
        return 'Invalid X-Server-Token';
      }

      return true;
    }
    private function buildResponse($data=null, $error=false)
    {
      $response = array(
        'response' => ($error == false) ? 'OK' : 'FAIL',
        'error' => $error,
        'request' => substr(strrchr($this->getRequest()->server->get('REQUEST_URI'),'/'), 1)
      );

      if( $data != null ) {
        $response['data'] = $data;
      }
      
      return $response;
    }
    private function getResponse($data=null, $error=false)
    {
      $response = new Response();
      $response->headers->set('Content-type', 'application/json; charset=utf-8');
      $response->setContent(json_encode($this->buildResponse($data, $error)));
      return $response;
    }
}
