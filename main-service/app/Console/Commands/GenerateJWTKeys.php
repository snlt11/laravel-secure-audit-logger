<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateJWTKeys extends Command
{
    protected $signature = 'jwt:generate-keys';
    protected $description = 'Generate RSA public/private keys for JWT (RS256)';

    public function handle()
    {
        $keyPath = storage_path('app/keys');

        if (!is_dir($keyPath)) {
            mkdir($keyPath, 0755, true);
        }

        $privateKeyFile = "$keyPath/private.key";
        $publicKeyFile = "$keyPath/public.key";

        $this->info('Generating RSA key pair...');

        // Generate private key
        $privateKey = openssl_pkey_new([
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        // Extract private key to a string
        openssl_pkey_export($privateKey, $privateKeyString);

        // Extract public key from the key pair
        $publicKeyDetails = openssl_pkey_get_details($privateKey);
        $publicKeyString = $publicKeyDetails["key"];

        // Save keys to files
        file_put_contents($privateKeyFile, $privateKeyString);
        file_put_contents($publicKeyFile, $publicKeyString);

        $this->info("✅ Keys generated:");
        $this->info("🔐 Private key: $privateKeyFile");
        $this->info("🔓 Public key:  $publicKeyFile");
    }
}
