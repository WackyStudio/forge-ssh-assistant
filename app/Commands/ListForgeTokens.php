<?php

namespace App\Commands;

use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ListForgeTokens extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'token:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the Laravel Forge tokens that are curently saved to the config file.';

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
        $tokens = $this->tokenHandler->readToken();

        if (empty($tokens)) {
            $this->info('There is no token saved to the config file at the moment');
            exit(1);
        }

        $this->line("");
        foreach ($tokens as $name => $token) {
            $this->info($name.' : '.$token);
        }
    }
}
