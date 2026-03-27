<?php
declare(strict_types=1);
namespace App\Exception;

class EmailNotVerifiedException extends \RuntimeException
{
    public function __construct(string $message, int $code = 403)
    {
        parent::__construct($message, $code);
    }
}
