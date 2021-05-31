<?php

namespace App\Commands;

use App\Forge;
use App\TokenHandler;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;
use NunoMaduro\LaravelConsoleMenu\Menu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;
use PhpSchool\CliMenu\Builder\SplitItemBuilder;
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

        /** @var Menu $menu */
        $menu = $this->menu('Forge SSH Assistant by Wacky Studio ')
            ->setTitleSeparator('_')
            ->setBackgroundColour('black')
            ->setForegroundColour('white');
            //->setUnselectedMarker('   ')
            //->setSelectedMarker('➡  ');

        foreach ($this->servers as $name => $servers) {
            if ($name !== array_key_first($this->servers)) {
                $menu->addStaticItem('');
            }

            $menu->addStaticItem('   ' . ucfirst($name) . ' servers:');
            $menu->addLineBreak('_', 1);
            $menu->addStaticItem('');

            $menu->addOptions($servers);
            $menu->addLineBreak('_', 1);
        }

        $ip = $menu->addStaticItem('')
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
    }

    public function fetchServersOfTokens()
    {

        foreach ($this->getTokens() as $tokenName => $token) {
            $this->forge->fetchServers($token);
            $servers = $this->forge->listServerNames();

            foreach ($servers as $ip => $serverName) {
                $servers[$ip] = $serverName;
            }

            $this->servers[$tokenName] = $servers;
        }

        ksort($this->servers);
    }

    /**
     * @param $ip
     */
    public function runSSH($ip): void
    {
        $connectTo = Arr::collapse($this->servers)[$ip];

        $this->info("Setting up SSH connection to {$connectTo}");
        $process = new Process(["ssh", "forge@{$ip}"]);
        $process->setTty(Process::isTtySupported());
        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();
        $this->info('SSH Connection closed - Bye!');
    }
}
