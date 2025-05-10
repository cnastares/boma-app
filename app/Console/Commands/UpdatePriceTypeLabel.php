<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePriceTypeLabel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price-type-label';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the price type label';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale=config('app.locale');
        $priceTypesLabel = [
            [
                'id' => 1,
                'label'=>null
            ],
            [
                'id' => 2,
                'label'=> [$locale => 'Free']
            ],
            [
                'id' => 3,
                'label'=> [$locale => 'Please Contact']
            ],
            [
                'id' => 4,
                'label'=> [$locale => 'Swap/Trade']
            ]
        ];

        foreach ($priceTypesLabel as  $type) {
            $existingType = DB::table('price_types')->where('id', $type['id'])->first();

            if ($existingType) {
                // Update the existing type
                DB::table('price_types')->where('id', $type['id'])->update([
                    'label' => json_encode($type['label']),
                ]);
            }
        }
        $this->info("Price type label updated successfully.");
    }
}
