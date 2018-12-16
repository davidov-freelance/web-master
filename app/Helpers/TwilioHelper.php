<?php

namespace App\Helpers;

use Config;
use Twilio;

class TwilioHelper {
    public static function sendSMS($number, $message) {
        $twilioConfig = Config::get('services.twilio');
    
        $client = new Twilio\Rest\Client($twilioConfig['sid'], $twilioConfig['token']);
    
        $message = $client->messages->create(
            $number,
            array(
                'from' => $twilioConfig['number'],
                'body' => $message
            )
        );
        
        return $message->sid;
    }
}
