<?php

/**
 * Web2All Signal handler class
 *
 * Class for handling signals, useful for daemon scripts which need
 * to exit on ^c or kill -HUP.
 * 
 * Sample usage:
 * $sh = new Web2All_Signal_Handler();
 * $sh->pcntlSignalSetup();
 * while(1){
 *   // do stuff
 *   sleep($time);
 *   $sh->pcntlSignalDispatch();
 *   if ($sh->pcntlSignalReceived()) break;
 * }
 *
 * @author Merijn van den Kroonenberg
 * @copyright (c) Copyright 2017 Web2All BV
 * @since 2017-08-22 
 */
class Web2All_Signal_Handler {
  
  /**
   * If pcntl is enabled we handle SIGTERM and SIGHUP to exit the daemon
   *
   * @var boolean
   */
  protected $pcntl_signal_received = false;
  
  /**
   * The last signal number we handled
   *
   * @var int
   */
  protected $pcntl_signal_num = null;
  
  /**
   * True if pcntl module is enabled
   *
   * @var boolean
   */
  protected $pcntl_enabled = false;
  
  /**
   * The logger instance.
   *
   * @var LoggerInterface
   */
  protected $logger =  null;
  
  /**
   * Signal handle which will exit our daemon loop
   * 
   * @param int $signo
   * @param mixed $signinfo  Only in some cases present as of PHP 7.1
   */
  public function pcntlSignalHandler($signo, $signinfo = null)
  {
    if($this->logger){
      $this->logger->debug('Web2All_Signal_handler->pcntlSignalHandler: signal '.$signo.' received');
    }
    $this->pcntl_signal_received = true;
    $this->pcntl_signal_num = $signo;
  }
  
  /**
   * Set up signal handling if supported
   * 
   * Do NOT call from constructor!
   * 
   * @param array $exit_signals  array of signal codes (int)
   *                             default: SIGHUP,SIGTERM,SIGINT
   */
  public function pcntlSignalSetup($exit_signals=null)
  { 
    if (function_exists('pcntl_signal')) {
      if(is_null($exit_signals)){
        // catch kill -HUP, kill and ctrl-c
        $exit_signals=array(SIGHUP,SIGTERM,SIGINT);
      }
      foreach($exit_signals as $sig){
        pcntl_signal($sig, array($this,'pcntlSignalHandler') );
      }
      $this->pcntl_enabled = true;
      $this->pcntl_signal_received = false;
      $this->pcntl_signal_num = null;
      if($this->logger){
        $this->logger->debug('Web2All_Signal_handler->pcntlSignalSetup: signal handling enabled');
      }
    }else{
      if($this->logger){
        $this->logger->debug('Web2All_Signal_handler->pcntlSignalSetup: signal handling NOT SUPPORTED');
      }
    }
  }
  
  /**
   * Handle signals
   * 
   * Call this regulary in your code, preferably after each sleep
   */
  public function pcntlSignalDispatch()
  {
    if($this->pcntl_enabled){
      pcntl_signal_dispatch();
    }
  }
  
  /**
   * Did we receive an exit signal
   * 
   * @return boolean
   */
  public function pcntlSignalReceived()
  {
    return $this->pcntl_signal_received;
  }
  
  /**
   * return the last signal number we received
   * 
   * @return int
   */
  public function getSignalReceived()
  {
    return $this->pcntl_signal_num;
  }
  
  /**
   * Sets a logger instance on the object.
   *
   * @param LoggerInterface $logger
   *
   * @return void
   */
  public function setLogger($logger)
  {
    $this->logger = $logger;
  }
}

?>