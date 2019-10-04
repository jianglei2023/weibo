<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //一对多关系 一个用户对应多条微博，一条微博对应一个用户
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
