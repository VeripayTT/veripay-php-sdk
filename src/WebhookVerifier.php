<?php

namespace VeripayTT\SDK;

use VeripayTT\SDK\Exceptions\VeriPayException;

class WebhookVerifier
{
    public static function verify(string $payload, string $headerSignature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $headerSignature)) {
            throw new VeriPayException('Invalid webhook signature.');
        }

        return true;
    }
}
