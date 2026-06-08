<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:tempfiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpieza automática de los archivos contenidos en app/temp/*';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = glob(storage_path('app/public/tmp/*'));

        foreach ($files as $file) {
            if (filemtime($file) < time() - 3600) { // Más de 1 hora
                $this->deleteRecursively($file);
            }
        }
    }

    protected function deleteRecursively($path)
    {
        try {
            if (is_dir($path)) {
                $items = glob($path . '/*');

                foreach ($items as $item) {
                    $this->deleteRecursively($item);
                }

                rmdir($path);
            } else {
                unlink($path);
            }
        } catch (\Exception $e) {
            error_log("Error al eliminar $path: " . $e->getMessage());
        }
    }

}
