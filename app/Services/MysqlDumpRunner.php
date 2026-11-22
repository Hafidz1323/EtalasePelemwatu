<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class MysqlDumpRunner
{
    /**
     * Jalankan proses mysqldump dengan argumen array dan environment variables.
     *
     * @param  array<int, string>  $command
     * @param  array<string, string>  $env
     */
    public function run(array $command, array $env = []): Process
    {
        $process = new Process($command, null, $env);
        $process->setTimeout(300);
        $process->run();

        return $process;
    }
}
