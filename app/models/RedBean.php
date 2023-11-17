<?php

namespace App\Models;

use R as RedBeanModel;

class User extends RedBeanModel
{
    private string $table = 'user';
    private string $primaryKey = 'id';

    public function getUser(int $userId): mixed
    {
        return self::find($this->table, $this->primaryKey . ' = ?', [$userId]);
    }
}
