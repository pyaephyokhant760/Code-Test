<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'profile',
    ];

    public function company()
    {
        return $this->belongsTo(company::class);
    }
}
