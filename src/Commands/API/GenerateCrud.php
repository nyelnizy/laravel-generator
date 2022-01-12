<?php

namespace InfyOm\Generator\Commands\API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generate {model_name?} {file_name?} {--prefix} {--graphql} {--all=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'wrapper around infyom labs api_scaffold';

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
        $file_name = $this->argument('file_name');
        $pfx = "";
        if ($this->option('prefix')) {
            $pfx = "--prefix=$model_name";
        }
        $is_graphql = $this->option('prefix');
        try {
            $model_files_path = config('infyom.laravel_generator.path.model_files');
            if (is_null($this->option('all'))) {
                if(is_null($model_name) || is_null($file_name)){
                    if(!is_null($model_name)){
                        $file_name = strtolower($model_name) . '.json';
                    }else{
                        throw  new \Exception('command usage, php artisan crud:generate ModelName model_file.json (model_file is optional if it has the same name as model)');
                    }
                    
                }
                $this->info("Generating Crud for $model_name...");
                $this->generateCrud($model_name, "$model_files_path/$file_name", $pfx,$is_graphql);
                $this->info("Generated Crud Successfully");
            } else {
                $models = str_getcsv($this->option('all'), ',');
                foreach ($models as $index => $model_name) {
                    $index++;
                    $file_path = "$model_files_path/".strtolower($model_name) . '.json';
                    if (file_exists($file_path)) {
                        $this->info("$index) Generating Crud for $model_name...");
                        $this->generateCrud($model_name,$file_path,$pfx,$is_graphql);
                        $this->info("Generated Successfully");
                    } else {
                        $this->info("$index) Skipping Crud for $model_name, $file_path does not exist");
                    }
                }
            }

        } catch (\Exception $ex) {
            $this->error("An error occurred : => ".$ex->getMessage());
        }
        return 0;
    }

    private function generateCrud(string $model_name, string $file_path, $pfx,$is_graphql)
    {
       if($is_graphql){
        Artisan::call("infyom:api $model_name
        --fieldsFile=$file_path --skip=views,menu,routes,controllers,tests
         --factory $pfx --no-interaction");
       }else{
        Artisan::call("infyom:api $model_name
        --fieldsFile=$file_path --skip=views,menu
        --seeder --factory $pfx --no-interaction");
       }
    }
}
