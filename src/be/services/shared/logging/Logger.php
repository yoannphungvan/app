<?php

namespace PROJECT\Services\Shared\Logging;

class Logger implements LoggerInterface {

  const SUCCESS  = 'success';
  const DEBUG    = 'debug';
  const INFO     = 'info';
  const WARNING  = 'warning';
  const ERROR    = 'error';
  const CRITICAL = 'critical';
  const BLOCKER  = 'blocker';

  protected $levelHierarchy = [
    self::DEBUG => 0,
    self::INFO => 1,
    self::SUCCESS => 2,
    self::WARNING => 3,
    self::ERROR => 4,
    self::CRITICAL => 5,
    self::BLOCKER => 6
  ];

  protected $minLevel;
  protected $mapping;

  public function __construct($app)
  {
    $loggerConfigs = $app['configs']['logger.configs'];
    if (empty($loggerConfigs['default-logger'])) {
      throw new \Exception('Missing default logger');
    }

    // Default level is the highest level
    $this->minLevel = max($this->levelHierarchy);

    if (isset($loggerConfigs['min-level']) && isset($this->levelHierarchy[$loggerConfigs['min-level']])) {
      $this->minLevel = $this->levelHierarchy[$loggerConfigs['min-level']];
    }

    foreach ($this->levelHierarchy as $level => $priority) {
      $this->mapping[$level] = $app[$loggerConfigs['default-logger']];

      if (isset($loggerConfigs['mapping'][$level]['logger'])) {
        $this->mapping[$level] = $app[$loggerConfigs['mapping'][$level]['logger']];
      }  
    }

  } 

  public function addSuccess($message, $code = null) 
  {
    if($this->isLevelLogged(self::SUCCESS)) {
      $this->mapping[self::SUCCESS]->addSuccess($message, $code);
    }
  }

  public function addDebug($message, $code = null) 
  {
    if($this->isLevelLogged(self::DEBUG)) {
      $this->mapping[self::DEBUG]->addDebug($message, $code);
    }
  }

  public function addInfo($message, $code = null)
  {
    if($this->isLevelLogged(self::INFO)) {
      $this->mapping[self::INFO]->addInfo($message, $code);
    }
  }

  public function addWarning($message, $code = null)
  {
    if($this->isLevelLogged(self::WARNING)) {
      $this->mapping[self::WARNING]->addWarning($message, $code);
    }
  }

  public function addError($message, $code = null)
  {
    if($this->isLevelLogged(self::ERROR)) {
      $this->mapping[self::ERROR]->addError($message, $code);
    }
  }

  public function addCritical($message, $code = null)
  {
    if($this->isLevelLogged(self::CRITICAL)) {
      $this->mapping[self::CRITICAL]->addCritical($message, $code);
    }
  }

  public function addBlocker($message, $code = null)
  {
    if($this->isLevelLogged(self::BLOCKER)) {
      $this->mapping[self::BLOCKER]->addBlocker($message, $code);
    }
  }

  private function isLevelLogged($level)
  {
    if ($this->levelHierarchy[$level] < $this->minLevel) {
      return false;
    }

    return true;
  }

  protected function sanitizeMessage($message) 
  {
    $title = '';

    if (is_array($message)) {
      if (isset($message['title']) && is_string($message['title'])) {
        $title = $message['title'];
      }

      if (isset($message['message'])) {
        $message = $message['message'];
      }
    }

    if (!is_string($message)) {
      $message = json_encode($message);
    }

    return [$title, $message];
  }
}
