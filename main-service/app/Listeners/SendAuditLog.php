<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class SendAuditLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $payload = [
            'type' => 'user_registered',
            'data' => [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'name' => $event->user->name,
                'created_at' => $event->user->created_at,
            ],
            'iat' => time(),
            'exp' => time() + 60, // expires in 1 min
            'iss' => env('AUDIT_APP_NAME')
        ];

        $privateKey = file_get_contents(storage_path('app/keys/private.key'));

        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        Http::withToken($jwt)->post(env('AUDIT_API_URL'), $payload);
    }
}
