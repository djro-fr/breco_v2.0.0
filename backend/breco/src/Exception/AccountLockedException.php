<?php
declare(strict_types=1);

namespace App\Exception;

class AccountLockedException extends \RuntimeException
{
    public function __construct(string $message = 'Compte verrouillé', int $code = 423)
    {
        parent::__construct($message, $code);
    }
}
