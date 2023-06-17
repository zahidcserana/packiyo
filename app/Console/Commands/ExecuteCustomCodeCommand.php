<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecuteCustomCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom-code:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs custom code from custom.php if found';

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
        $path = base_path('custom.php');

        if (file_exists($path)) {
            include($path);
        }
    }
}
