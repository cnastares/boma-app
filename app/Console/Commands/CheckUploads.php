<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica configuraci\xC3\xB3n y permisos del sistema de archivos';

    public function handle()
    {
        $disk = config('filesystems.default');
        $path = config('filesystems.disks.' . $disk . '.root');

        $this->info("Disk: {$disk}");
        $this->info("Root path: {$path}");

        if (!is_dir($path)) {
            $this->error('Directorio no existe');
            return Command::FAILURE;
        }

        if (!is_writable($path)) {
            $this->error('Directorio sin permisos de escritura');
            return Command::FAILURE;
        }

        $this->info('Directorio v\xC3\xA1lido y escribible');
        $test = Storage::disk($disk)->put('test.txt', 'OK');
        if ($test) {
            $this->info('Escritura correcta');
            Storage::disk($disk)->delete('test.txt');
            return Command::SUCCESS;
        }

        $this->error('Fallo al escribir');
        return Command::FAILURE;
    }
}
