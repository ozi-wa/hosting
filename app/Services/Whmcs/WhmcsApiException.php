<?php

namespace App\Services\Whmcs;

use RuntimeException;

class WhmcsApiException extends RuntimeException
{
    public function __construct(string $message, public readonly array $payload = [])
    {
        parent::__construct($message);
    }
}
