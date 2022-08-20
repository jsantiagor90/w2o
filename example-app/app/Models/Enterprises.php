<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprises extends Model
{
    protected $fillable = [
        'name',
        'document',
        'phone',
        'email',
        'zipcode',
        'state',
        'city',
        'neighborhood',
        'street',
        'number',
        'complement',
    ];
}
