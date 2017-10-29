<?php
namespace RA\DocBundle\Model;

/**
 * DocumentException
 */
class DocumentException extends \Exception
{
    private $errors;

    public function __construct(array $errors, string $message, int $code)
    {
        parent::__construct($message, $code);
        $this->setErrors($errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }
}
