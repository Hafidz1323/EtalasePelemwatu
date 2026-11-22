<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset password akun admin dan cabut semua token aktif';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $this->error("User dengan email '{$email}' tidak ditemukan.");

            return Command::FAILURE;
        }

        if ($user->role !== 'admin') {
            $this->error("User dengan email '{$email}' bukan akun admin.");

            return Command::FAILURE;
        }

        $newPassword = Str::password(16, symbols: false);
        $user->password = Hash::make($newPassword);
        $user->save();

        // Cabut semua token sesi admin
        $user->tokens()->delete();

        $this->info("Password untuk admin '{$user->email}' berhasil direset.");
        $this->warn("Password baru: {$newPassword}");
        $this->line('Semua token sesi admin telah dicabut. Catat password ini segera.');

        return Command::SUCCESS;
    }
}
