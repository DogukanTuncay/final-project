<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeFullModel extends Command
{
    protected $signature = 'make:full-model {name} {--types=*}';
    protected $description = 'Create a full model structure with modular architecture';

    protected $defaultTypes = ['Admin', 'Api'];

    protected $baseDirectories = [
        'Interfaces' => [
            'Repositories' => [], // Tipler buraya eklenecek
            'Services' => [],     // Tipler buraya eklenecek
        ],
        'Repositories' => [],     // Tipler buraya eklenecek
        'Services' => [],         // Tipler buraya eklenecek
        'Http' => [
            'Controllers',
            'Requests',
            'Resources',
        ]
    ];

    public function handle()
    {
        $name = $this->argument('name');
        $types = $this->option('types');
        $types = !empty($types) ? array_map('ucfirst', $types) : $this->defaultTypes;

        $this->info('🚀 Starting to create full model structure for: ' . $name);
        $this->line('------------------------------------------------');

        // Model ve Migration oluştur
        $this->createModelAndMigration($name);

        $this->info('📂 Creating directories and files for types: ' . implode(', ', $types));
        $this->line('------------------------------------------------');

        // Her tip için ayrı dosyaları oluştur
        $bar = $this->output->createProgressBar(count($types));
        $bar->start();

        foreach ($types as $type) {
            $this->createTypeSpecificFiles($name, $type);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Özet rapor
        $this->showSummary();
    }
    
    private function updateDirectoryStructure($types)
    {
        // Interfaces altındaki her klasöre tipleri ekle
        $this->baseDirectories['Interfaces']['Repositories'] = $types;
        $this->baseDirectories['Interfaces']['Services'] = $types;
        
        // Ana dizinlere tipleri ekle
        $this->baseDirectories['Repositories'] = $types;
        $this->baseDirectories['Services'] = $types;
    }
    private function createModelAndMigration($name)
    {
        $this->info('📝 Creating Model and Migration...');
        
        $this->call('make:model', [
            'name' => $name,
            '-m' => true
        ]);

        $this->createdFiles[] = [
            'type' => 'Model',
            'path' => "app/Models/{$name}.php"
        ];
        $this->createdFiles[] = [
            'type' => 'Migration',
            'path' => "database/migrations/*_create_" . Str::snake(Str::pluralStudly($name)) . "_table.php"
        ];
    }
    private function createTypeSpecificFiles($name, $type)
    {
        $this->createWithInfo('Repository Interface', $name, $type, function() use ($name, $type) {
            $content = $this->getRepositoryInterfaceTemplate($name, $type);
            $path = app_path("Interfaces/Repositories/{$type}/{$name}RepositoryInterface.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Service Interface', $name, $type, function() use ($name, $type) {
            $content = $this->getServiceInterfaceTemplate($name, $type);
            $path = app_path("Interfaces/Services/{$type}/{$name}ServiceInterface.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Repository', $name, $type, function() use ($name, $type) {
            $content = $this->getRepositoryTemplate($name, $type);
            $path = app_path("Repositories/{$type}/{$name}Repository.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Service', $name, $type, function() use ($name, $type) {
            $content = $this->getServiceTemplate($name, $type);
            $path = app_path("Services/{$type}/{$name}Service.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Controller', $name, $type, function() use ($name, $type) {
            $content = $this->getControllerTemplate($name, $type);
            $path = app_path("Http/Controllers/{$type}/{$name}Controller.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Request', $name, $type, function() use ($name, $type) {
            $content = $this->getRequestTemplate($name, $type);
            $path = app_path("Http/Requests/{$type}/{$name}Request.php");
            $this->createFile($path, $content);
            return $path;
        });

        $this->createWithInfo('Resource', $name, $type, function() use ($name, $type) {
            $content = $this->getResourceTemplate($name, $type);
            $path = app_path("Http/Resources/{$type}/{$name}Resource.php");
            $this->createFile($path, $content);
            return $path;
        });
    }

    private function createInterfaces($name)
    {
        // Repository Interface
        $repositoryInterfaceContent = $this->getRepositoryInterfaceTemplate($name);
        $repositoryInterfacePath = app_path("Interfaces/Repositories/{$name}RepositoryInterface.php");
        $this->createFile($repositoryInterfacePath, $repositoryInterfaceContent);

        // Service Interface
        $serviceInterfaceContent = $this->getServiceInterfaceTemplate($name);
        $serviceInterfacePath = app_path("Interfaces/Services/{$name}ServiceInterface.php");
        $this->createFile($serviceInterfacePath, $serviceInterfaceContent);
    }

    private function createWithInfo($fileType, $name, $type, $callback)
    {
        $this->line("Creating {$type} {$fileType} for {$name}...");
        $path = $callback();
        $this->createdFiles[] = [
            'type' => "{$type} {$fileType}",
            'path' => $path
        ];
    }

    private function createFile($path, $content)
    {
        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
        File::put($path, $content);
    }


    private function getRepositoryInterfaceTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Interfaces\\Repositories\\{$type};\n\ninterface {$name}RepositoryInterface\n{\n    public function all();\n    public function find(\$id);\n    public function create(array \$data);\n    public function update(\$id, array \$data);\n    public function delete(\$id);\n}";
    }

    private function getServiceInterfaceTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Interfaces\\Services\\{$type};\n\ninterface {$name}ServiceInterface\n{\n    public function all();\n    public function find(\$id);\n    public function create(array \$data);\n    public function update(\$id, array \$data);\n    public function delete(\$id);\n}";
    }

    private function getRepositoryTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Repositories\\{$type};\n\nuse App\\Models\\{$name};\nuse App\\Interfaces\\Repositories\\{$type}\\{$name}RepositoryInterface;\nuse App\\Repositories\\BaseRepository;\n\nclass {$name}Repository extends BaseRepository implements {$name}RepositoryInterface\n{\n    public function __construct({$name} \$model)\n    {\n        parent::__construct(\$model);\n    }\n}";
    }

    private function getServiceTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Services\\{$type};\n\nuse App\\Interfaces\\Services\\{$type}\\{$name}ServiceInterface;\nuse App\\Interfaces\\Repositories\\{$type}\\{$name}RepositoryInterface;\nuse App\\Services\\BaseService;\n\nclass {$name}Service extends BaseService implements {$name}ServiceInterface\n{\n    public function __construct({$name}RepositoryInterface \$repository)\n    {\n        parent::__construct(\$repository);\n    }\n}";
    }

    private function getControllerTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Http\\Controllers\\{$type};\n\nuse App\\Http\\Controllers\\Controller;\nuse App\\Interfaces\\Services\\{$name}ServiceInterface;\nuse App\\Http\\Requests\\{$type}\\{$name}Request;\nuse App\\Http\\Resources\\{$type}\\{$name}Resource;\n\nclass {$name}Controller extends Controller\n{\n    protected \$service;\n\n    public function __construct({$name}ServiceInterface \$service)\n    {\n        \$this->service = \$service;\n    }\n\n    public function index()\n    {\n        \$items = \$this->service->all();\n        return {$name}Resource::collection(\$items);\n    }\n\n    public function store({$name}Request \$request)\n    {\n        \$item = \$this->service->create(\$request->validated());\n        return new {$name}Resource(\$item);\n    }\n\n    public function show(\$id)\n    {\n        \$item = \$this->service->find(\$id);\n        return new {$name}Resource(\$item);\n    }\n\n    public function update({$name}Request \$request, \$id)\n    {\n        \$item = \$this->service->update(\$id, \$request->validated());\n        return new {$name}Resource(\$item);\n    }\n\n    public function destroy(\$id)\n    {\n        return \$this->service->delete(\$id);\n    }\n}";
    }

    private function getRequestTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Http\\Requests\\{$type};\n\nuse App\\Http\\Requests\\BaseRequest;\n\nclass {$name}Request extends BaseRequest\n{\n    public function rules()\n    {\n        return [\n            //\n        ];\n    }\n}";
    }

    private function getResourceTemplate($name, $type)
    {
        return "<?php\n\nnamespace App\\Http\\Resources\\{$type};\n\nuse App\\Http\\Resources\\BaseResource;\n\nclass {$name}Resource extends BaseResource\n{\n    public function toArray(\$request)\n    {\n        \$translated = \$this->getTranslated(\$this->resource);\n        \n        return array_merge(\$translated, [\n            'id' => \$this->id,\n            // Add other non-translatable attributes here\n        ]);\n    }\n}";
    }

    private function showSummary()
    {
        $this->info('✅ Full model structure created successfully!');
        $this->line('------------------------------------------------');
        $this->info('📋 Summary of created files:');
        
        $headers = ['Type', 'Path'];
        $rows = $this->createdFiles;
        
        $this->table($headers, $rows);

        $this->line('------------------------------------------------');
        $this->info('🔍 Next steps:');
        $this->line('1. Update the migration file with your schema');
        $this->line('2. Implement your business logic in the Service classes');
        $this->line('3. Add validation rules in the Request classes');
        $this->line('4. Customize the Resource classes for your API responses');
        $this->line('5. Register your routes in the appropriate route file');
        $this->line('6. Update your ServiceProvider to bind interfaces');
        $this->newLine();
        
        if ($this->confirm('Would you like to see the file structure in tree format?', true)) {
            $this->showTreeStructure();
        }
    }

    private function showTreeStructure()
    {
        $this->info('📁 Generated File Structure:');
        $this->line('app/');
        $this->line('├── Models/');
        $this->line('│   └── ' . $this->argument('name') . '.php');
        $this->line('├── Interfaces/');
        $this->line('│   ├── Repositories/');
        $this->line('│   │   ├── Admin/');
        $this->line('│   │   └── Api/');
        $this->line('│   └── Services/');
        $this->line('│       ├── Admin/');
        $this->line('│       └── Api/');
        $this->line('├── Repositories/');
        $this->line('│   ├── Admin/');
        $this->line('│   └── Api/');
        $this->line('├── Services/');
        $this->line('│   ├── Admin/');
        $this->line('│   └── Api/');
        $this->line('└── Http/');
        $this->line('    ├── Controllers/');
        $this->line('    │   ├── Admin/');
        $this->line('    │   └── Api/');
        $this->line('    ├── Requests/');
        $this->line('    │   ├── Admin/');
        $this->line('    │   └── Api/');
        $this->line('    └── Resources/');
        $this->line('        ├── Admin/');
        $this->line('        └── Api/');
    }

}