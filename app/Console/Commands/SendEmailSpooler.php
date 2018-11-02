<?php

namespace App\Console\Commands;

use App\EmailSpooler;
use App\RayX;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailSpooler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia todos los correos pendiente en cola';

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
        $emailSpooler = EmailSpooler::where('status', EmailSpooler::STATUS_PENDING)->get();

        foreach ($emailSpooler as $email) {

            $class = $email->class;

            $data = $email->getParams();
            $data['recipients'] = $email->getRecipients();

            $emalInstance = new $class($data);

            Mail::send($emalInstance);

            $email->status = EmailSpooler::STATUS_COMPLETE;
            $email->save();
        }

        $this->imageStorage();
    }

    private function imageStorage()
    {
        $imageToStorage = RayX::query()->whereNotNull('base64')->get();

        foreach ($imageToStorage as $image) {

            $upload = base64_decode($image->base64);
            $path = public_path($image->url);

            $dir = explode('/', $image->url);
            unset($dir[ count($dir) - 1 ]);
            $dir = implode('/', $dir);
            $dir = public_path($dir);

            if (! is_dir($dir)) {
                mkdir($dir);
            }

            file_put_contents($path, $upload);

            $image->base64 = null;
            $image->save();
        }
    }
}
