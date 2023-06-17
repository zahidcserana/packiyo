<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUserAccessTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-access-token {user_id} {token_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and prints access token for the user';

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
        $user = User::findOrFail($this->argument('user_id'));

        $this->line(__('Your new token: :token', ['token' => $user->createToken($this->argument('token_name'))->plainTextToken]));
    }
}
