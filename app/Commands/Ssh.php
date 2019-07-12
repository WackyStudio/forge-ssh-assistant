<?php

namespace App\Commands;

use App\Forge;
use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
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

    /**
     * List of servers accessible with saved tokens
     * @var array
     */
    private $servers = [];


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
        $this->fetchServersOfTokens();

        $ip = $this->menu('Forge SSH Assistant by Wacky Studio 🤵', $this->servers)
            ->setTitleSeparator('=')
            ->setBackgroundColour('black')
            ->setForegroundColour('white')
            ->addLineBreak('_', 1)
            ->setUnselectedMarker('   ')
            ->setSelectedMarker('➡  ')
            ->open();

        if ($ip === null) {
            $this->info('Good bye!');
            exit(0);
        }
        $this->runSSH($ip);
        $this->handle();
    }

    public function getTokens()
    {
        $tokens = $this->tokenHandler->readToken();
        if (empty($tokens)) {
            $this->error('No Laravel Forge token has been added!');
            exit(1);
        }

        return $tokens;

        // $this->forge->fetchServers($tokens);
    }

    public function fetchServersOfTokens()
    {

        foreach ($this->getTokens() as $token_name => $token) {
            $this->forge->fetchServers($token);
            $servers_of_token = $this->forge->listServerNames();

            foreach ($servers_of_token as $ip => $server_name) {
                $servers_of_token[$ip] = "$server_name ({$token_name})";
            }

            array_push($this->servers, $servers_of_token);
        }

        $this->servers = Arr::collapse($this->servers);
    }

    /**
     * @param $ip
     */
    public function runSSH($ip): void
    {
        $this->info("Setting up SSH connection to {$this->servers[$ip]}");
        $process = new Process("ssh forge@{$ip}");
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();
        $this->info('SSH Connection closed - Bye!');
    }
}
