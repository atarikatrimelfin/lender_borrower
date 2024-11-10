<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_limit',
    ];
}
