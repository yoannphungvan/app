<?php

namespace PROJECT\Services\Shared\Logging;

interface LoggerInterface {

	public function addSuccess($message, $code = null);

	public function addDebug($message, $code = null);

    public function addInfo($message, $code = null);

    public function addWarning($message, $code = null);

    public function addError($message, $code = null);

    public function addCritical($message, $code = null);

    public function addBlocker($message, $code = null);

}