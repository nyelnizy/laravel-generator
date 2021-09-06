<?php

namespace InfyOm\Generator\Commands\API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrudRollBack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:rollback {model_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rollback crud ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model_name = $this->argument('model_name');
        try{
            Artisan::call("infyom:rollback $model_name api --no-interaction");
            $this->info("Rollback Successful");
        }catch(\Exception $ex){
            $this->error("An error occurred : => ".$ex->getMessage());
        }
        return 0;
    }
}
