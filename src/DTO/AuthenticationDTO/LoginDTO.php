<?php

namespace App\DTO\AuthenticationDTO;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoginDTO
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    public readonly ?string $password;

    #[Assert\NotBlank]
    #[Assert\NotNull]
    public readonly ?string $username;

    protected $fields;

    protected $validator;

    public function __construct(array $fields,ValidatorInterface $validator)
    {
        $this->fields = $fields;

        $this->validator = $validator;
    }

    private function makeObjects()
    {
        foreach ($this->fields as $field=>$value)
        {
            if (property_exists($this,$field))
            {
                $this->{$field} = $value;
            }
        }
    }

    public function doValidate()
    {
        $this->makeObjects();
        $errors = $this->validator->validate($this);
        if ($errors->count() > 0)
        {
            throw (new \Exception(json_encode($errors)));
        }
    }
}