<?php

namespace App\Models;

use App\Services\BackupService;
use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class BackupDestinationStatus extends Model
{
    use Sushi;

    public function getRows(): array
    {
        return BackupService::getBackupDestinationStatusData();
    }
}
