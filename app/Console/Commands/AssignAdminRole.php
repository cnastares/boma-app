<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate All permission and assign it to a admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info("Generating Shield resources for panel");
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin'
        ]);

        $this->info("Assigning super-admin role to admin users...");
        $users = User::where('is_admin', 1)->get();

        if ($users->isEmpty()) {
            $this->warn("No admin users found.");
            return;
        }

        foreach ($users as $user) {
            try {
                Artisan::call('shield:super-admin', [
                    '--user' => $user->id,
                    '--panel' => 'admin',
                ]);

                $this->info("Assigned super-admin role to User ID: {$user->id}");
            } catch (\Exception $e) {
                $this->error("Failed to assign role to User ID: {$user->id} - Error: {$e->getMessage()}");
            }
        }

        $this->info("Admin role assignment process completed.");
    }
}
