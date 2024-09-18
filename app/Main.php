<?php

namespace App;

require FileService::class;

use App\Services\FileService;

class Main{

    public function run(){
        echo "Enter the word you want to hyphenate:\n";
        $word = trim(fgets(STDIN));
    }
}

$app = new Main();
$app->run();