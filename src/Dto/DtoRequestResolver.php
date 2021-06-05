<?php

namespace Fd\HslBundle\Dto;

use Fd\HslBundle\Exception\DtoValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Use to resolve a request to DTO
 */
class DtoRequestResolver implements ArgumentValueResolverInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Return true if Dto is implements RequestDtoInterface
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        try
        {
            $reflection = new \ReflectionClass($argument->getType());
            return $reflection->implementsInterface(DtoRequestInterface::class);
        }
        catch(\ReflectionException $e)
        {
            /**
             * Because some controller have to use value-type argument value instead of class-type argument,
             * without try-catch it will throw uncaught exception.
             * 
             * So here i will catch it and just return false.
             */
            return false;
        }
        
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $dto = new $class($request);

        //do symfony validation here
        $errors = $this->validator->validate($dto);
        
        if(count($errors) > 0)
        {
            $errorMessages = [];

            foreach($errors as $error)
            {
                $errorMessages [] = $error->getPropertyPath() . ' => ' . $error->getMessage();
            }
            throw new DtoValidationException(['messages' => $errorMessages]);
        }

        yield $dto;
    }
}