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
    protected $signature = 'token:add {name} {token}';

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
        $name = $this->argument('name');

        if (empty($token)) {
            $this->error('ERROR: No token given');
            exit(1);
        }

        if (empty($name)) {
            $this->error('ERROR: No name given for the token');
            exit(1);
        }
        
        $this->tokenHandler->addToken($name, $token);
        $this->info('The token has been added');
    }
}
