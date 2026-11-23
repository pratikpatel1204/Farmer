<?php

namespace App\Support;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging (HTTP v1) se mobile push. Koi extra composer package nahi chahiye.
 *
 * Setup: Firebase console > Project settings > Service accounts > "Generate new private key"
 *        JSON file ko storage/app/firebase.json par rakhein (ya .env FCM_CREDENTIALS=path).
 * File na ho to push chupchap skip hota hai, website / API pe koi asar nahi.
 */
class Push
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    /** Ek user ke saare phones par push. $data me sirf string values (FCM ka rule). */
    public static function toUser(int $userId, string $title, string $body, array $data = []): void
    {
        $credentials = self::credentials();
        if (! $credentials) {
            return;
        }

        $tokens = DeviceToken::where('user_id', $userId)->pluck('token');
        if ($tokens->isEmpty()) {
            return;
        }

        $accessToken = self::accessToken($credentials);
        if (! $accessToken) {
            return;
        }

        $url  = 'https://fcm.googleapis.com/v1/projects/' . $credentials['project_id'] . '/messages:send';
        $data = array_map('strval', $data);

        foreach ($tokens as $token) {
            try {
                $response = Http::withToken($accessToken)->timeout(8)->post($url, [
                    'message' => [
                        'token'        => $token,
                        'notification' => ['title' => $title, 'body' => $body],
                        'data'         => $data,
                        'android'      => [
                            'priority'     => 'high',
                            'notification' => ['channel_id' => config('kisanmandi_app.fcm_channel', 'kisanmandi_default'), 'sound' => 'default'],
                        ],
                        'apns'         => ['payload' => ['aps' => ['sound' => 'default']]],
                    ],
                ]);

                // App uninstall / token purana: table se hata do
                if ($response->status() === 404 || str_contains((string) $response->body(), 'UNREGISTERED')) {
                    DeviceToken::where('token', $token)->delete();
                } elseif ($response->failed()) {
                    Log::warning('FCM push failed: ' . $response->status() . ' ' . $response->body());
                } else {
                    DeviceToken::where('token', $token)->update(['last_used_at' => now()]);
                }
            } catch (\Throwable $e) {
                Log::warning('FCM push error: ' . $e->getMessage());
            }
        }
    }

    private static function credentials(): ?array
    {
        $path = (string) config('kisanmandi_app.fcm_credentials');
        if ($path === '') {
            return null;
        }

        $full = str_starts_with($path, '/') ? $path : base_path($path);
        if (! is_file($full)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($full), true);

        return (is_array($json) && ! empty($json['private_key']) && ! empty($json['client_email']) && ! empty($json['project_id']))
            ? $json
            : null;
    }

    /** Google OAuth access token (55 minute cache; fail ho to null, cache me nahi rukta) */
    private static function accessToken(array $credentials): ?string
    {
        return Cache::remember('fcm_access_token_' . md5($credentials['client_email']), now()->addMinutes(55), function () use ($credentials) {
            try {
                $now     = time();
                $encode  = fn ($value) => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
                $header  = $encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
                $payload = $encode(json_encode([
                    'iss'   => $credentials['client_email'],
                    'scope' => self::SCOPE,
                    'aud'   => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                    'iat'   => $now,
                    'exp'   => $now + 3600,
                ]));

                $signature = '';
                if (! openssl_sign($header . '.' . $payload, $signature, $credentials['private_key'], 'sha256WithRSAEncryption')) {
                    return null;
                }

                $response = Http::asForm()->timeout(10)->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $header . '.' . $payload . '.' . $encode($signature),
                ]);

                return $response->json('access_token');
            } catch (\Throwable $e) {
                Log::warning('FCM access token error: ' . $e->getMessage());

                return null;
            }
        });
    }
}
