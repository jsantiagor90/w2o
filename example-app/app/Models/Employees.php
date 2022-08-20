<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $fillable = [
        'enterprise_id',
        'name',
        'phone',
        'email',
        'birth_date'
    ];
}
