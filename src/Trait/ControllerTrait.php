<?php

namespace App\Trait;

use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    public function checkAccess($attribute,$object,$message=null)
    {
        $message = $message ?: 'Access Denied.';
        if (!$this->isGranted($attribute, $object)) {
            throw new \Exception(json_encode($message),Response::HTTP_FORBIDDEN);
        }
    }
}
