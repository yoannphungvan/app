<?php

namespace PROJECT\Services\Shared\Logging;

class LoggerMonolog extends Logger {

  private $monolog;

  public function __construct($monologService)
  {
    $this->monolog = $monologService;
  } 

  public function addSuccess($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addInfo('SUCCESS: ' . (!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addDebug($message, $code = null) 
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addDebug((!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addInfo($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addInfo((!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addWarning($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addWarning((!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addError($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addError((!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addCritical($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addError('CRITICAL: ' . (!empty($title) ? $title . ' - ' : '') . $message);
  }

  public function addBlocker($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->monolog->addError('BLOCKER: ' . (!empty($title) ? $title . ' - ' : '') . $message);
  }
}
