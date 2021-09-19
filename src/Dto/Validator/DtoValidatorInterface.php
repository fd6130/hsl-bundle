<?php

namespace Fd\HslBundle\Dto\Validator;

use Fd\HslBundle\Dto\DtoRequestInterface;

interface DtoValidatorInterface
{
    /**
     * @param DtoRequestInterface $dto The data transfer object class
     * 
     * @throws DtoValidationException
     */
    public function validate(DtoRequestInterface $dto);
}