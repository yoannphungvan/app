<?php

namespace PROJECT\Services\Shared\Logging;

class LoggerSlack extends Logger {

  private $slack;

  const SUCCESS_ICON = ':white_check_mark:';
  const DEBUG_ICON = ':mag:';
  const INFO_ICON = ':memo:';
  const WARNING_ICON = ':warning:';
  const ERROR_ICON = ':x:';
  const CRITICAL_ICON = ':exclamation:';
  const BLOCKER_ICON = ':sos:';

  private $channels = [];

  public function __construct($configs, $slackService)
  {
    $loggerConfigs = $configs['logger.configs'];
    if (empty($loggerConfigs['slack']['default'])) {
      throw new \Exception('Missing default slack channel');
    }
    
    $this->slack = $slackService;
    $this->retailer = $configs['retailers.name'];
    $this->env = $configs['env'];

    foreach ($this->levelHierarchy as $level => $priority) {
      $this->channels[$level] = $loggerConfigs['slack']['default'];

      if (isset($loggerConfigs['slack'][$level])) {
        $this->channels[$level] = $loggerConfigs['slack'][$level];
      }  
    }
  } 

  public function addSuccess($message, $code = null) 
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::SUCCESS], $this->getContext($title), $message, null , self::SUCCESS_ICON);
  }

  public function addDebug($message, $code = null) 
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::DEBUG], $this->getContext($title), $message, null , self::DEBUG_ICON);
  }

  public function addInfo($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::INFO], $this->getContext($title), $message, null , self::INFO_ICON);
  }

  public function addWarning($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::WARNING], $this->getContext($title), $message, null , self::WARNING_ICON);
  }

  public function addError($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::ERROR], $this->getContext($title), $message, null , self::ERROR_ICON);
  }

  public function addCritical($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::CRITICAL], $this->getContext($title), $message, null , self::CRITICAL_ICON);
  }

  public function addBlocker($message, $code = null)
  {
    list($title, $message) = $this->sanitizeMessage($message);
    $this->slack->sendMessageToChannel($this->channels[static::BLOCKER], $this->getContext($title), $message, null , self::BLOCKER_ICON);
  }

  private function getContext($title)
  {
    return $this->retailer . ': ' . $title . ' (' . $this->env . ')';
  }
}
