<?php

namespace App\Models;

use App\Services\BackupService;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * @property string $path
 * @property string $disk
 */
class BackupDestination extends Model
{
    use Sushi;

    public function getRows(): array
    {
        $data = [];

        foreach (BackupService::getDisks() as $disk) {
            $data = array_merge($data, BackupService::getBackupDestinationData($disk));
        }

        return $data;
    }
}
