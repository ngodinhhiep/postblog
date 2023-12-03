<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use App\Mail\NewEmailConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendEmailConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $incomingData)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->incomingData['sendTo'])->send(new NewEmailConfirmation(['name' => $this->incomingData['name']]));
    }
}
