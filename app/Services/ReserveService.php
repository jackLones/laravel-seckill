<?php

namespace App\Services;

use App\Models\ReserveUser;
use App\Models\ReserveInfo;
use Carbon\Carbon;

class ReserveService
{
    protected ReserveUser $reserve;

    public function __construct(ReserveUser $reserve)
    {
        $this->reserve = $reserve;
    }


}
