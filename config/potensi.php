<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tampilkan Kontak di Respons Publik
    |--------------------------------------------------------------------------
    | Jika true, field whatsapp akan disertakan dalam PotensiPublicResource.
    | Set ke true jika tombol "Hubungi" di frontend membutuhkan nomor WA.
    | Default: true (tombol hubungi aktif di landing page).
    |
    */
    'show_contact' => env('POTENSI_SHOW_CONTACT', true),
];
