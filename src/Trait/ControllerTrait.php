<?php

namespace App\Trait;

trait ControllerTrait
{
    public function unAuthorizedResponse()
    {
        return $this->json(['message' => 'access denied'], 403);
    }
}