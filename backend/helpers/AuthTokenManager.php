<?php

namespace helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException; // For general decoding errors

class AuthTokenManager
{
    // IMPORTANT: This secret key MUST be a strong, random, long string (e.g., 32+ characters).
    // Store it securely, preferably in environment variables (e.g., a .env file loaded by Dotenv library),
    // and NEVER hardcode it in production.
    // For demonstration purposes, we'll assign it here, but read the security notes below.
    private static string $secretKey = JWT_SECRET;
    private static string $algorithm = 'HS256'; // HS256 is a common symmetric algorithm (HMAC SHA256)

    // Optional: A method to set the secret key if you're loading it from an external source
    public static function setSecretKey(string $key): void
    {
        self::$secretKey = $key;
    }

    /**
     * Generates a secure JWT token for a user.
     *
     * @param array $user An associative array containing 'id' and 'email'.
     * @return string The generated JWT token.
     * @throws Exception If the secret key is not set.
     */
    public static function generateToken(array $user): string
    {
        if (empty(self::$secretKey)) {
            throw new \Exception("JWT secret key not set. Please configure it.");
        }

        $issuedAt = time(); // Current timestamp
        $expirationTime = $issuedAt + (24 * 60 * 60); // Token valid for 24 hours (86400 seconds)

        $payload = [
            'iat' => $issuedAt,       // Issued At: Time when the token was issued
            'exp' => $expirationTime, // Expiration Time: When the token expires
            'sub' => (string)$user['id'], // Subject: Typically the user's unique ID. Cast to string is good practice.
            'email' => $user['email'], // Custom claim: User's email
            'iss' => 'your_application_domain.com', // Issuer: Your application's domain
            'aud' => 'your_api_client_audience',    // Audience: Who the token is intended for
            'jti' => base64_encode(random_bytes(16)) // JWT ID: Unique ID for this token (useful for blacklisting)
        ];

        // Encode the payload into a JWT using the secret key and algorithm
        return JWT::encode($payload, self::$secretKey, self::$algorithm);
    }

    /**
     * Validates and decodes a JWT token.
     *
     * @param string $token The JWT token string.
     * @return array|false The decoded payload if valid, false otherwise.
     */
    public static function validateToken(string $token): array|false
    {
        if (empty(self::$secretKey)) {
            // Log this error, as it indicates a serious configuration issue.
            error_log("JWT secret key not set during validation attempt. This is a critical error.");
            return false;
        }

        try {
            // Decode the token. The library will:
            // 1. Verify the signature using the provided key and algorithm.
            // 2. Check 'exp' (expiration) claim.
            // 3. Check 'nbf' (not before) claim if present.
            // 4. Check 'iat' (issued at) claim if present with leeway.
            $decoded = JWT::decode($token, new Key(self::$secretKey, self::$algorithm));

            // JWT::decode returns a stdClass object. Cast it to an associative array if preferred.
            return (array)$decoded;

        } catch (ExpiredException $e) {
            // Token is expired. Log this for debugging or auditing.
            error_log("Token validation failed: Token has expired. Message: " . $e->getMessage());
            return false;
        } catch (SignatureInvalidException $e) {
            // Token signature is invalid (token was tampered with or signed with a different key).
            error_log("Token validation failed: Invalid signature. Token might be tampered. Message: " . $e->getMessage());
            return false;
        } catch (UnexpectedValueException $e) {
            // Other unexpected values, e.g., malformed token, invalid claims.
            error_log("Token validation failed: Unexpected value or malformed token. Message: " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            // Catch any other general exceptions during decoding.
            error_log("An unexpected error occurred during token validation: " . $e->getMessage());
            return false;
        }
    }
}