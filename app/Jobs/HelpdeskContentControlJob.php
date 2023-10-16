<?php

namespace App\Jobs;

use App\Tools\Builderall\BuilderallAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HelpdeskContentControlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param App\User;
     */
    private $user;

    /**
     * @param string
     */
    private $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\User $user, string $status)
    {
        $this->user   = $user;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BuilderallAccount::updateAllStatus($this->user, $this->status, 'QUEUE');
    }
}
