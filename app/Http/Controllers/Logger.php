<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;

class Logger {

    private $path;
    public function __construct($path=null)
    {
        $this->path = $path;
        if (!(File::exists($this->path))) {
            File::put($this->path, '');
        }
    }

    public function log($type, $text = '', $timestamp = true)
    {
        if(gettype($text) == 'array'){
            $text = json_encode($text);
        }
        if ($timestamp) {
            $datetime = date("d-m-Y H:i:s");
            $text = "$datetime, $type: $text \r\n\r\n";
        } else {
            $text = "$type\r\n\r\n";
        }

        error_log($text, 3, $this->path);
    }

}
