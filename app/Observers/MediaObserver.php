<?php

namespace App\Observers;

class MediaObserver
{
    public function deleting($record){
       $record->modification()->delete();
    }
}
