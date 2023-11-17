<?php

namespace App\Controllers\Base;

use App\Foundation\Database\Query;
use Exception;
use R;

trait QueryTrait
{
    public static function queryGetBulider()
    {
        $query = new Query();
        if ($query instanceof Query) {
            return new Query();
        }

        throw new Exception("Error Processing Request Class Query", 1);
    }

    public static function redbeanBulider()
    {
        return (new R() instanceof R) ? new R() : null;
    }
}
