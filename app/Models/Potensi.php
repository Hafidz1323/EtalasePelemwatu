<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potensi extends Model
{
    use HasFactory;

    protected $table = 'potensi';

    protected $fillable = [
        'category_id',
        'name',
        'owner_name',
        'whatsapp',
        'address',
        'description',
        'location',
        'status',
        'latitude',
        'longitude',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images()
    {
        return $this->hasMany(PotensiImage::class, 'potensi_id');
    }
}

