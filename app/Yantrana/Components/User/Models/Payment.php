<?php

namespace App\Yantrana\Components\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Yantrana\Base\BaseModel;

class Payment extends BaseModel
{
    use HasFactory;
    protected $fillable = ['user_id'];

}
