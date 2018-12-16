<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

class EmailHelper {
    
    public static function sendMail($email, $subject, $body)
    {
        $mail = new PHPMailer();
    
        $mail->isSMTP();
        // Use it for testing
//        $mail->SMTPDebug = 2;
        $mail->Host = env('MAIL_HOST');
        $mail->Port = env('MAIL_PORT');
        $mail->SMTPSecure = env('MAIL_ENCRYPTION');
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';
    
        $clientId = env('MAIL_CLIENT_ID');
        $clientSecret = env('MAIL_CLIENT_SECRET');
        $provider = new Google([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]);
    
        $oAuth = new OAuth([
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => env('MAIL_REFRESH_TOKEN'),
            'userName' => env('MAIL_OAUTH_USER_EMAIL'),
        ]);
        
        $mail->setOAuth($oAuth);
    
        $mail->setFrom(env('MAIL_USERNAME'));
        $mail->CharSet = 'utf-8';
        $mail->addAddress($email);
    
    
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        return $mail->send();
    }
}
