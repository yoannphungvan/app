<?php

namespace PROJECT\Services\Shared\Messaging;

use Twilio\Rest\Client;

class Twilio {

  private $client;

  /**
   * contructor
   */
  public function __construct($accountSid, $authToken)
  {
    $this->client = new Client($accountSid, $authToken);
  }

  public function sendSMS($from, $to, $message)
  {
    $sms = $this->client->account->messages->create(
      $to,
      [
        'from' => $from, 
        'body' => $message
      ]
    );
  }
}
