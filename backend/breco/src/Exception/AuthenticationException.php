<?php
declare(strict_types=1);
namespace App\Exception;

class AuthenticationException extends \RuntimeException
{
    public function __construct(string $message, int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
