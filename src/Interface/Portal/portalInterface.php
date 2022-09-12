<?php

namespace App\Interface\Portal;

use App\DTO\Portal\PortalDTO;

interface portalInterface
{
    public function payOrder(PortalDTO $portalDTO);
}
