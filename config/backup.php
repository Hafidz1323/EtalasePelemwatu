<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Path ke Binary mysqldump
    |--------------------------------------------------------------------------
    | Path atau nama binary mysqldump untuk backup database MySQL/MariaDB.
    | Contoh Windows (XAMPP): 'C:\\xampp\\mysql\\bin\\mysqldump.exe'
    | Contoh Linux/macOS: '/usr/bin/mysqldump' atau 'mysqldump' jika ada di PATH.
    |
    */
    'mysqldump_path' => env('BACKUP_MYSQLDUMP_PATH', 'mysqldump'),
];
