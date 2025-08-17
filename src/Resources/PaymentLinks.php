<?php

namespace VeripayTT\SDK\Resources;

use VeripayTT\SDK\VeriPayClient;

class PaymentLinks
{
    protected VeriPayClient $client;

    public function __construct(VeriPayClient $client)
    {
        $this->client = $client;
    }

    public function create(array $data): array
    {
        return $this->client->request('POST', 'v1/payment-links', [
            'json' => $data
        ]);
    }

    public function list(): array
    {
        return $this->client->request('GET', 'v1/payment-links');
    }

    public function get(string $linkId): array
    {
        return $this->client->request('GET', "v1/payment-links/{$linkId}");
    }
}
