<?php

namespace Fd\HslBundle\Dto\Validator;

use Fd\HslBundle\Dto\DtoRequestInterface;
use Fd\HslBundle\Exception\DtoValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DtoValidator implements DtoValidatorInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param DtoRequestInterface $dto The data transfer object class
     * 
     * @throws DtoValidationException
     */
    public function validate(DtoRequestInterface $dto)
    {
        $errors = $this->validator->validate($dto);
        
        if(count($errors) > 0)
        {
            $errorMessages = [];

            foreach($errors as $error)
            {
                $errorMessages [] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            }

            throw new DtoValidationException($errorMessages);
        }
    }
}