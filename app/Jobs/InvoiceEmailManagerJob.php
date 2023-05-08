<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailManagerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $details =$this->details;
        $template = @$details['template'];
        // $html = view('template-mail.'.$template, compact('details'))->render();
        Mail::send($this->details['view'],['order' => $this->details['order']],function($message)use($details){
            $message->to($details['email'])
                ->subject($details['subject'])
                ->from($details['from'], env('MAIL_FROM_NAME'));
            // ->subject('Welcome to the Tutorials Point');
            // ->to('email@example.com', 'Mr. Example');
            // ->sender('email@example.com', 'Mr. Example');
            // ->returnPath('email@example.com');
            // ->cc('email@example.com', 'Mr. Example');
            // ->bcc('email@example.com', 'Mr. Example');
            // ->replyTo('email@example.com', 'Mr. Example');
            // ->priority(2);
            // ->attach('path/to/attachment.txt');
            // ->embed('path/to/attachment.jpg');
        });
    }
}
