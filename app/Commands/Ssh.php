<?php

namespace App\Commands;

use App\Forge;
use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Process\Process;
use TheSeer\Tokenizer\Token;

class Ssh extends Command
{

    /**
     * @var Forge
     */
    private $forge;

    /**
     * @var TokenHandler
     */
    private $tokenHandler;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ssh';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'SSH into your servers from a list';


    public function __construct(Forge $forge, TokenHandler $tokenHandler)
    {
        parent::__construct();
        $this->forge = $forge;
        $this->tokenHandler = $tokenHandler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getToken();

        $ip = $this->menu('Forge SSH Assistant by Wacky Studio 🤵', $this->forge->listServerNames())
            ->setTitleSeparator('=')
            ->setBackgroundColour('black')
            ->setForegroundColour('white')
            ->addLineBreak('_', 1)
            ->setUnselectedMarker('   ')
            ->setSelectedMarker('➡  ')
            ->open();

        if($ip === null){
            $this->info('Good bye!');
            exit(0);
        }
        $this->runSSH($ip);
        $this->handle();
    }

    public function getToken(): void
    {
        $token = $this->tokenHandler->readToken();
        if (empty($token)) {
            $this->error('No Laravel Forge token has been added!');
            exit(1);
        }
        $this->forge->fetchServers($token);
    }

    /**
     * @param $ip
     */
    public function runSSH($ip): void
    {
        $this->info("Setting up SSH connection to {$this->forge->listServerNames()[$ip]}");
        $process = new Process("ssh forge@{$ip}");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();
        $this->info('SSH Connection closed - Bye!');
    }


}
