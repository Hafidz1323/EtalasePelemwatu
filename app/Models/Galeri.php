<?php

namespace App\Models;

use App\Services\MediaStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 'galeri';

    protected $fillable = [
        'image_path',
        'caption',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function toArray(): array
    {
        $array = parent::toArray();
        if (isset($array['image_path']) && $array['image_path']) {
            $array['image_path'] = MediaStorage::url($array['image_path']);
        }

        return $array;
    }
}
