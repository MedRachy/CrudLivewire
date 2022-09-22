<?php

namespace Medrachy\CrudLivewire\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class BuildCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:build {name}';

    protected $description = 'Generate the CRUD livewire component for a modal';

    // table name 
    protected $tableName = null;

    // modal name 
    protected $modelName = null;

    // className 
    protected $className = null;

    public function __construct()
    {
        parent::__construct();
        $this->file = new FileSystem();
    }

    public function handle()
    {
        $this->tableName = $this->argument('name');
        $this->modelName = $this->buildModelName();
        $this->className = $this->modelName . 'Crud';

        // check if table exists in the dababase 
        if (!$this->tableExists()) {
            $this->error("table name `{$this->tableName}` dont exist");
            return false;
        }
        // check if modal exists 
        if (!class_exists("App\\Models\\" . $this->modelName)) {
            $this->error("model class `{$this->modelName}` dont exist");
            return false;
        }

        // Generate the livewire class
        $this->generateLivewireClass();

        // Generate the livewire view 
        $this->generateLivewireView();

        return true;
    }

    private function generateLivewireClass()
    {
        $fileOrigin = __DIR__ . '/../stubs/livewire.crud.stub';
        $fileDestinsation = base_path('/app/Http/Livewire/' . $this->className . '.php');

        // check if class already exists
        if ($this->file->exists($fileDestinsation)) {
            $this->info('This class file already exists : ' . $this->className . '.php');
            $this->info('Aborting creation.');
            return false;
        }

        // create Livewire directory if dont exists 
        $this->file->ensureDirectoryExists(base_path('/app/Http/Livewire'));

        // replace the variables for the stub 
        $map = [
            '{{modelName}}' => $this->modelName,
            '{{modalInstanceName}}' => Str::lower($this->modelName),
            '{{className}}' => $this->className,
            '{{viewName}}' => Str::kebab($this->className)
        ];
        $fileOriginalString = Str::swap(
            $map,
            $this->file->get($fileOrigin)
        );

        // generate the class file 
        $this->file->put($fileDestinsation, $fileOriginalString);
        $this->info('Livewire class file created : ' . $fileDestinsation);
    }

    private function generateLivewireView()
    {
        $fileOrigin = __DIR__ . '/../stubs/livewire.view.crud.stub';
        $fileDestination = base_path('/resources/views/livewire/' . Str::kebab($this->className) . '.blade.php');

        // check if view already exists
        if ($this->file->exists($fileDestination)) {
            $this->info('This view file already exists : ' . Str::kebab($this->className) . '.blade.php');
            $this->info('Aborting creation.');
            return false;
        }

        // create livewire directory if dont exists 
        $this->file->ensureDirectoryExists(base_path('/resources/views/livewire'));

        // generate the view file
        $this->file->copy($fileOrigin, $fileDestination);
        $this->info('Livewire view file created : ' . $fileDestination);
    }

    protected function tableExists()
    {
        return Schema::hasTable($this->tableName);
    }

    private function buildModelName()
    {
        return Str::studly(Str::singular($this->tableName));
    }
}

/*
    [x] check if the table provided as argument exists 
    [x] get the class name from the table name    
    [x] check if the model exsits 
    [x] create folder Livewire if dont exists
    [x] generate the livewire class/view for the model
    [x] update livewire class 
    [x] use fillable attributes to show in table records / rules 
    [x] start with default inputypes 
    [ ] edit styling for the view (table)
    // ----------
    [x] publish on github
    [ ] write README doc 
    [ ] publish on packagist 
    
*/
