<?php

namespace App\DTO\ShipmentDTO;
use App\Entity\Shipment\Shipment;
use App\Entity\Shipment\ShipmentItem;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShipmenAndShipmentItemUpdateDTO
{
    #[Assert\NotBlank]
    #[Assert\Choice(Shipment::STATUS,message: "not a valid status")]
    public readonly ?string $status;

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
        if (count($errors) >0) {
            $messages = [];
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            throw new \Exception(json_encode($messages));
        }
    }
}