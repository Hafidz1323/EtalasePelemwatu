<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Potensi extends Model
{
    use HasFactory;

    /** Status yang tampil di halaman publik. */
    public const STATUS_PUBLIK = ['approved'];

    protected $table = 'potensi';

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'owner_name',
        'whatsapp',
        'email',
        'address',
        'description',
        'location',
        'jenis',
        'jam_operasional',
        'status',
        'latitude',
        'longitude',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'deletion_requested_at',
    ];

<<<<<<< HEAD
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'reviewed_at' => 'datetime',
    ];

    public function user()
=======
    public function owner()
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
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
<<<<<<< HEAD

    /**
     * Helper scope untuk potensi yang disetujui tampil di publik
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'aktif']);
    }
=======
>>>>>>> 99bae7f599c1565d45f22896e47f8da1e83dd8c2
}
