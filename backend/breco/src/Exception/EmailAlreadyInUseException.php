<?php
declare(strict_types=1);
namespace App\Exception;

class EmailAlreadyInUseException extends \RuntimeException
{
    public function __construct(string $message, int $code = 422)
    {
        parent::__construct($message, $code);
    }
}
