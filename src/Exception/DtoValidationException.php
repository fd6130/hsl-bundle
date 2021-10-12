<?php
declare(strict_types=1);

namespace Fd\HslBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class DtoValidationException extends HttpException
{
    protected $context = [];

    public function __construct(?string $message = '', array $context = [])
    {
        $message = !empty($message) ? $message : 'DTO validation fail.';
        $this->context = $context;

        parent::__construct(400, $message, null, [] , 0);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}