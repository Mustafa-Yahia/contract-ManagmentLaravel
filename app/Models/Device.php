<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category_id', 'description', 'price', 'availability_status', 'img'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
