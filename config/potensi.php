<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tampilkan Kontak di Respons Publik
    |--------------------------------------------------------------------------
    | Jika true, field whatsapp akan disertakan dalam PotensiPublicResource.
    | Set ke true jika tombol "Hubungi" di frontend membutuhkan nomor WA.
    | Default: false — lindungi privasi nomor WA warga di endpoint publik.
    |
    */
    'show_contact' => env('POTENSI_SHOW_CONTACT', false),
];
