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
    protected $signature = 'token:remove';

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
        $this->tokenHandler->removeToken();
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
