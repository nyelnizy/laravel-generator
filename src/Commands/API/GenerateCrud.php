<?php

namespace InfyOm\Generator\Commands\API;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;

class GenerateCrud extends Command
{
    protected $types = [
        "increments" => "ID",
        "string" => "String",
        "varchar" => "String",
        "json" => "String",
        "text" => "String",
        "longText" => "String",
        "date" => "String",
        "dateTime" => "String",
        "integer" => "Int",
        "boolean" => "Boolean",
        "float" => "Float",
        "double" => "Float",
    ];
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
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
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
        $is_graphql = $this->option('graphql');
        try {
            $model_files_path = config('infyom.laravel_generator.path.model_files');
            if (is_null($this->option('all'))) {
                if (is_null($model_name) || is_null($file_name)) {
                    if (!is_null($model_name)) {
                        $file_name = strtolower($model_name) . '.json';
                    } else {
                        throw  new \Exception('command usage, php artisan crud:generate ModelName model_file.json (model_file is optional if it has the same name as model)');
                    }
                }
                $this->info("Generating Crud for $model_name...");
                $file_path = "$model_files_path/$file_name";
                if(file_exists($file_path)){
                    $this->generateCrud($model_name, $file_path, $pfx, $is_graphql);
                    $this->info("Generated Crud Successfully");
                }else{
                    $this->info("Skipping Crud for $model_name, $file_path does not exist"); 
                }
            } else {
                $models = str_getcsv($this->option('all'), ',');
                foreach ($models as $index => $model_name) {
                    $index++;
                    $file_path = "$model_files_path/" . strtolower($model_name) . '.json';
                    if (file_exists($file_path)) {
                        $this->info("$index) Generating Crud for $model_name...");
                        $this->generateCrud($model_name, $file_path, $pfx, $is_graphql);
                        $this->info("Generated Successfully");
                    } else {
                        $this->info("$index) Skipping Crud for $model_name, $file_path does not exist");
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->error("An error occurred : => " . $ex->getMessage());
        }
        return 0;
    }

    private function generateCrud(string $model_name, string $file_path, $pfx, $is_graphql)
    {
        if ($is_graphql) {
            Artisan::call("infyom:api $model_name
        --fieldsFile=$file_path --skip=views,menu,routes,controllers,tests
         --factory $pfx --no-interaction");
            $this->info("Generating Graphql types...");
            $contents = json_decode(file_get_contents($file_path));
            $this->generateTypes($contents, $model_name);
            $this->files->delete(app_path("Requets"));
        } else {
            Artisan::call("infyom:api $model_name
        --fieldsFile=$file_path --skip=views,menu
        --seeder --factory $pfx --no-interaction");
        }
    }

    private function generateTypes(array $eloquent_schema, string $model_name)
    {
        $fields = "";
        $total = count($eloquent_schema);
        foreach($eloquent_schema as $index=>$schema){
            $name = $schema->name;
            $type = $schema->dbType;
            $required = false;
            if(property_exists($schema,"validations")){
             $required = str_contains($schema->validations,'required');
            }
            if($name==="id"){
                $required = true;
            }
            $type = $this->types[strtolower($type)].($required?'!':'');
            $fields_contents = $this->getStubContents(['FIELD_NAME'=>$name,'FIELD_TYPE'=>$type], 'field');
            if($index == $total-1){
                $fields.="\t".$fields_contents;
            }else{
                if($index==0){
                    $fields.=$fields_contents."\n";  
                }else{
                    $fields.="\t".$fields_contents."\n";
                }
            }
        }

        $variables = ['MODEL' => $this->getSingularClassName($model_name), 'FIELDS' => $fields];
        $contents = $this->getStubContents($variables, 'schema');
        $path = base_path('graphql/models').'\\' .strtolower($model_name) . '_schema.graphql';

        $this->makeDirectory(dirname($path));

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("Schema File : {$path} created");
        } else {
            $this->info("Schema File : {$path} already exits, delete first");
        }
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath($stub)
    {
        return __DIR__ . "/../../../templates/graphql/$stub.stub";
    }

    public function getStubContents($variables, $stub)
    {
        $path = $this->getStubPath($stub);
        $contents = file_get_contents($path);
        foreach ($variables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }
        return $contents;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
