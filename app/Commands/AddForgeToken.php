<?php

namespace App\Commands;

use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AddForgeToken extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'token:add {token}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a Laravel Forge token <comment>(For communication with your Laravel Forge account)</comment>';

    /**
     * @var TokenHandler
     */
    private $tokenHandler;

    public function __construct(TokenHandler $tokenHandler)
    {
        parent::__construct();
        $this->tokenHandler = $tokenHandler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = $this->argument('token');
        if(empty($token)){
            $this->error('ERROR: No token given');
            exit(1);
        }
        $this->tokenHandler->addToken($token);
        $this->info('The token has been added');
    }

}
