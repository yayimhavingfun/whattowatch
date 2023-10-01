<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Film;
use App\Support\CommentsRepository;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Film $film)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CommentsRepository $repository): void
    {
        $this->film->comments()->saveMany($repository->getComments($this->film->id));
    }
}
