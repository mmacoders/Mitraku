<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use GuzzleHttp\Client as GuzzleClient;

class GmailService
{
    protected $gmail;

    public function __construct()
    {
        // Create a custom Guzzle HTTP client with SSL verification disabled
        // This is a workaround for SSL certificate issues on Windows
        $guzzleClient = new GuzzleClient([
            'verify' => false, // Disable SSL verification - NOT recommended for production
        ]);

        $client = new Client();
        $client->setApplicationName('E-PKS Gmail');
        $client->setClientId(config('services.gmail.client_id'));
        $client->setClientSecret(config('services.gmail.client_secret'));
        $client->setAccessType('offline');
        $client->setScopes(Gmail::GMAIL_SEND);
        $client->refreshToken(config('services.gmail.refresh_token'));
        
        // Set the custom HTTP client
        $client->setHttpClient($guzzleClient);

        $this->gmail = new Gmail($client);
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
            $rawMessageString = "From: Admin PKS <" . config('services.gmail.email') . ">\r\n";
            $rawMessageString .= "To: <$to>\r\n";
            $rawMessageString .= "Subject: $subject\r\n";
            $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
            $rawMessageString .= $body;

            $rawMessage = strtr(base64_encode($rawMessageString), ['+' => '-', '/' => '_']);

            $message = new Message();
            $message->setRaw($rawMessage);

            return $this->gmail->users_messages->send("me", $message);
        } catch (\Exception $e) {
            \Log::error('Gmail Service Error: ' . $e->getMessage());
            throw $e;
        }
    }
}