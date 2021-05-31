<?php

namespace App\Commands;

use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class RemoveForgeToken extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'token:remove {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a Laravel Forge token <comment>(Communication with your Laravel Forge account will no longer be possible)</comment>';

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
        $name = $this->argument('name');

        if (empty($name)) {
            $this->error('ERROR: No name given for the token to remove');
            exit(1);
        }

        $this->tokenHandler->removeToken($name);
        $this->info('The token has been removed');
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
