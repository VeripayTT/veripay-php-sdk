<?php

namespace VeripayTT\SDK\Facades;

use Illuminate\Support\Facades\Facade;

class VeriPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \VeripayTT\SDK\VeriPayClient::class;
    }
}
