<?php

namespace App;

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

    public function addToken($token)
    {
        $this->prepareConfigFile();
        Storage::put('.config/fssh/config.json', json_encode([
            'token' => $token,
        ]));
    }

    public function removeToken()
    {
        $this->prepareConfigFile();
        Storage::put('.config/fssh/config.json', json_encode([
            'token' => '',
        ]));
    }

    public function readToken()
    {
        $this->prepareConfigFile();
        $config = json_decode(Storage::get('.config/fssh/config.json'), true);
        return (isset($config['token']) ? $config['token']:'');
    }

    private function prepareConfigFile()
    {
        if(!Storage::has('.config')){
            Storage::makeDirectory('.config');
        }
        if(!Storage::has('.config/fssh')){
            Storage::makeDirectory('.config/fssh');
        }
        if(!Storage::has('config/fssh/config.json')){
            /*Storage::put('.config/fssh/config.json', json_encode([
                'token' => '',
            ]));*/
        }
    }
}