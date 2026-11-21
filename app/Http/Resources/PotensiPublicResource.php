<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource untuk endpoint publik GET /api/potensi dan GET /api/potensi/{id}.
 *
 * Field yang TIDAK disertakan (data pribadi):
 *   owner_name, email, address, reviewed_by, reviewed_at,
 *   rejection_reason, status.
 *
 * Field whatsapp bersifat opsional via config('potensi.show_contact').
 * Aktifkan jika tombol "Hubungi" di frontend membutuhkan nomor WA.
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
                'path' => $img->image_path,
            ])
            ),
        ];

        // Sertakan whatsapp hanya jika dikonfigurasi (tombol "Hubungi")
        if (config('potensi.show_contact', true)) {
            $data['whatsapp'] = $this->whatsapp;
        }

        return $data;
    }
}
