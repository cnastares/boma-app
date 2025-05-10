<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Traits\PaypalSubscription;
use Illuminate\Console\Command;

class PaypalInitializePlanCommand extends Command
{
    use PaypalSubscription;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'paypal:initialize-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize a new PayPal plan';

    public function __construct()
    {
        // Call the parent constructor to initialize the command
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $plans = Plan::whereNull('paypal_plan_id')->get();

        foreach ($plans as $plan) {
            $this->createPlan($plan);
        }

        $this->createWebHook();
        // https://webhook-test.com/bc4d436bd10f36687a5f0b65a9c7c9f3
    }
}
