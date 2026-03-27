<?php
declare(strict_types=1);
namespace App\Exception;

class VerificationException extends \RuntimeException
{
    public function __construct(string $message, int $code = 400)
    {
        parent::__construct($message, $code);
    }
}
