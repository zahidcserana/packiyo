<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries\LogRotationCompression;

class RotateCompressLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log-rotate-compress';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will rotate and compress logs';

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
        $rotationCompression = new LogRotationCompression();
        $rotationCompression->process();
    }
}

