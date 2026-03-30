<?php
declare(strict_types=1);

namespace App\Exception;

class TooManyAttemptsException extends \RuntimeException
{
    public function __construct(string $message = 'Accès temporairement bloqué', int $code = 429)
    {
        parent::__construct($message, $code);
    }
}
