<?php

namespace App\Interface\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

interface JWTManagementInterface
{
    public function getTokenUser(
        UserInterface $user,
        Request $request
    );
}