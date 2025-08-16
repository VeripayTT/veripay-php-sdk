<?php

namespace VeripayTT\SDK\Resources;

use VeripayTT\SDK\VeriPayClient;

class Payments
{
    protected VeriPayClient $client;

    public function __construct(VeriPayClient $client)
    {
        $this->client = $client;
    }

    public function list(): array
    {
        return $this->client->request('GET', '/v1/payments');
    }

    public function getStatus(string $paymentId): array
    {
        return $this->client->request('GET', "/v1/payments/{$paymentId}/status");
    }

    public function submitProof(string $paymentId, array $data): array
    {
        return $this->client->request('POST', "/v1/payments/{$paymentId}/proof", [
            'json' => $data
        ]);
    }
}
