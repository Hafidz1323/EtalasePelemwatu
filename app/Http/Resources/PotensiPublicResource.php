<?php

namespace App\Http\Resources;

use App\Services\MediaStorage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource untuk endpoint publik GET /api/potensi dan GET /api/potensi/{id}.
 *
 * Field yang TIDAK disertakan (data pribadi):
 *   owner_name, email, address, reviewed_by, reviewed_at,
 *   rejection_reason, status.
 *
 * Field whatsapp disertakan secara default (config('potensi.show_contact') = true)
 * untuk kebutuhan tombol "Hubungi" / kontak pembeli ke UMKM.
 */
class PotensiPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            'jenis' => $this->jenis,
            'jam_operasional' => $this->jam_operasional,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'category' => $this->whenLoaded('category', fn () => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'path' => MediaStorage::url($img->image_path),
            ])
            ),
        ];

        // Sertakan whatsapp sesuai konfigurasi potensi.show_contact (default: true)
        if (config('potensi.show_contact')) {
            $data['whatsapp'] = $this->whatsapp;
        }

        return $data;
    }
}
