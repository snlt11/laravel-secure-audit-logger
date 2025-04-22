<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class AuditLogController extends Controller
{
    public function store(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Authorization token missing.'], 401);
        }

        try {
            $decoded = $this->decodeToken($token);

            if (!$this->isValidIssuer($decoded)) {
                return response()->json(['message' => 'Invalid token issuer.'], 403);
            }

            if (!$this->isAuthorizedIP($request->ip())) {
                return response()->json(['message' => 'Unauthorized IP address.'], 403);
            }

            AuditLog::create([
                'type' => $decoded->type ?? 'undefined',
                'data' => json_encode((array) ($decoded->data ?? [])),
            ]);

            return response()->json(['message' => 'Audit log saved.']);
        } catch (\Exception $e) {
            Log::warning('JWT decode failed: ' . $e->getMessage());

            return response()->json(['message' => 'Invalid token.'], 401);
        }
    }

    private function decodeToken(string $token)
    {
        return JWT::decode($token, new Key(env('AUDIT_SECRET_KEY'), 'HS256'));
    }

    private function isValidIssuer($decoded): bool
    {
        return isset($decoded->iss) && $decoded->iss === env('AUDIT_APP_NAME');
    }

    private function isAuthorizedIP(string $ip): bool
    {
        return $ip === env('MAIN_APP_IP');
    }
}
