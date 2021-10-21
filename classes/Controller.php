<?php

use Illuminate\Support;  // https://laravel.com/docs/5.8/collections - provides the collect methods & collections class
use LSS\Array2Xml;

require_once('Exporter.php');
include('../factory/Formatter.php');

class Controller {

    private $exporter;

    public function __construct($args) {
        $this->args = $args;
        $this->exporter = new Exporter();
    }

    public function export($type, $format) {

        $data = [];

        $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
        $search = $this->args->filter(function($value, $key) use ($searchArgs) {
            return in_array($key, $searchArgs);
        });

        switch ($type) {
            case 'playerstats':
                $data = $this->exporter->getPlayerStats($search);
                break;
            case 'players':
                $data = $this->exporter->getPlayers($search);
                break;
        }

        if (! $data) {
            exit("Error: No data found!");
        }

        return new Formatter($data, $format);
    }
}