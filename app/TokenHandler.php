<?php

namespace App;

use App\Exceptions\NoConfigFileFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class TokenHandler
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {

        $this->filesystem = $filesystem;
    }

    public function addToken($name, $token)
    {
        $this->prepareConfigFile();

        $tokens = $this->readToken();

        if ($tokens === null) {
            $tokens = [$name => $token];
        } else {
            $tokens[$name] = $token;
        }

        Storage::put('.config/fssh/config.json', json_encode($tokens));
    }

    public function removeToken($name)
    {
        $this->prepareConfigFile();
        $tokens = $this->readToken();

        unset($tokens[$name]);

        Storage::put('.config/fssh/config.json', json_encode($tokens));
    }

    public function readToken()
    {
        if (!Storage::has('.config/fssh/config.json')) {
            throw new NoConfigFileFoundException('No config file found, this will be created when you add a token.');
        }

        $config = json_decode(Storage::get('.config/fssh/config.json'), true);
        return $config;
    }

    private function prepareConfigFile()
    {
        if (!Storage::has('.config')) {
            Storage::makeDirectory('.config');
        }

        if (!Storage::has('.config/fssh')) {
            Storage::makeDirectory('.config/fssh');
        }
        if (!Storage::has('.config/fssh/config.json')) {
            Storage::put('.config/fssh/config.json', json_encode([
            ]));
        }
    }
}
