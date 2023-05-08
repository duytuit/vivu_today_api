<?php

namespace App\Console\Commands;

use App\Models\Campain;
use App\Services\SendEmailService;
use Illuminate\Console\Command;

class SendMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

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
     * @return int
     */
    public function handle(SendEmailService $sendEmailService)
    {
        try {
                $campains = Campain::findByType('email');
                if ($campains) {
                    foreach ($campains as $key => $value) {
                        $sendEmailService->send($value);
                    }
                }
        } catch (\Exception $e) {
            dd($e);
        }
        return true;
    }
}
