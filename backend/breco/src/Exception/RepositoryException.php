<?php
declare(strict_types=1);
namespace App\Exception;

class RepositoryException extends \RuntimeException
{
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
