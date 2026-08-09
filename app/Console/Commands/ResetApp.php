<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan migrate fresh, seed, dan membersihkan folder storage photos dan selfies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses reset aplikasi...');

        // 1. Jalankan migrate:fresh --seed
        $this->info('Menjalankan migrate:fresh dan db:seed...');
        $this->call('migrate:fresh', ['--seed' => true]);

        // 2. Bersihkan storage/app/public/photos
        $this->info('Membersihkan folder storage/app/public/photos...');
        $photosFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('photos');
        foreach ($photosFiles as $file) {
            if (basename($file) !== '.gitignore') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        // 3. Bersihkan storage/app/public/selfies
        $this->info('Membersihkan folder storage/app/public/selfies...');
        $selfiesFiles = \Illuminate\Support\Facades\Storage::disk('public')->files('selfies');
        foreach ($selfiesFiles as $file) {
            if (basename($file) !== '.gitignore') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        $this->info('Reset aplikasi berhasil dilakukan! Database dan storage sudah bersih.');
    }
}
