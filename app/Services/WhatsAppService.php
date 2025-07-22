<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected $twilio;
    protected $from;

    public function __construct()
    {
        $this->twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $this->from = config('services.twilio.whatsapp_from');
    }

    public function enviarMensaje($to, $mensaje)
    {
        try {
            $this->twilio->messages->create(
                "whatsapp:$to",
                [
                    'from' => $this->from,
                    'body' => $mensaje
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}
