<?php

namespace VeripayTT\SDK\Resources;

use VeripayTT\SDK\VeriPayClient;

class Invoices
{
    protected VeriPayClient $client;

    public function __construct(VeriPayClient $client)
    {
        $this->client = $client;
    }

    public function list(): array
    {
        return $this->client->request('GET', '/api/v1/invoices');
    }

    public function getStatus(string $invoiceId): array
    {
        return $this->client->request('GET', "/api/v1/invoices/{$invoiceId}/status");
    }

   
}
