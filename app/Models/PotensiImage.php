<?php

namespace App\Models;

use App\Services\MediaStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotensiImage extends Model
{
    use HasFactory;

    protected $table = 'potensi_images';

    protected $fillable = [
        'potensi_id',
        'image_path',
    ];

    public function potensi()
    {
        return $this->belongsTo(Potensi::class, 'potensi_id');
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        if (isset($array['image_path']) && $array['image_path']) {
            $array['image_path'] = MediaStorage::url($array['image_path']);
        }

        return $array;
    }
}
