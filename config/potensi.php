<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tampilkan Kontak di Respons Publik
    |--------------------------------------------------------------------------
    | Jika true (default), field whatsapp akan disertakan dalam PotensiPublicResource
    | agar calon pembeli / pengunjung dapat menghubungi nomor WhatsApp UMKM.
    | Set POTENSI_SHOW_CONTACT=false jika ingin menyembunyikan nomor WA dari API publik.
    |
    */
    'show_contact' => env('POTENSI_SHOW_CONTACT', true),
];
