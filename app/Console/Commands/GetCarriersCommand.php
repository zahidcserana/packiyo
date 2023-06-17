<?php

namespace App\Console\Commands;

use App\Jobs\GetCarriersJob;
use Illuminate\Console\Command;

class GetCarriersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-carriers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs shipping carriers and methods';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function handle()
    {
        dispatch_sync(new GetCarriersJob());
    }
}
