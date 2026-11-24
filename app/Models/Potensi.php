<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potensi extends Model
{
    use HasFactory;

    protected $table = 'potensi';

    protected $fillable = [
        'user_id',
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

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images()
    {
        return $this->hasMany(PotensiImage::class, 'potensi_id');
    }

    /**
     * Helper scope untuk potensi yang disetujui tampil di publik
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'aktif']);
    }
}
