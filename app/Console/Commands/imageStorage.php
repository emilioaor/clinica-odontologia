<?php

namespace App\Console\Commands;

use App\RayX;
use Illuminate\Console\Command;

class imageStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $imageToStorage = RayX::query()->whereNotNull('base64')->get();

        foreach ($imageToStorage as $image) {

            $upload = base64_decode($image->base64);
            $path = public_path($image->url);

            $dir = explode('/', $image->url);
            unset($dir[ count($dir) - 1 ]);
            $dir = implode('/');
            dd($dir);
            if (! is_dir($path)) {
                mkdir($path);
            }

            file_put_contents($path, $upload);
        }

    }
}
