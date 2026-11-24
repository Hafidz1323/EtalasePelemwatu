<?php

return [
    // Akun admin awal untuk seeder. Atur lewat .env (jangan di-commit).
    'email' => env('ADMIN_EMAIL', 'admin@pelemwatu.desa.id'),
    'name' => env('ADMIN_NAME', 'Administrator Desa'),
    'password' => env('ADMIN_PASSWORD'),
];
