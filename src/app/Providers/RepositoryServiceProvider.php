<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Desteklenen tüm tipler
     */
    protected $types = ['Admin', 'Api', 'Auth'];

    public function register(): void
    {
        $this->bindAllInterfaces();
    }

    /**
     * Tüm interface'leri bind et
     */
/**
 * Tüm interface'leri otomatik olarak bind et
 */
private function bindAllInterfaces(): void
{
    $namespaces = ['Admin', 'Api', 'Auth']; // Admin, API ve Auth için

    // Repositories bağlama işlemi
    foreach ($namespaces as $namespace) {
        $repositoryInterfaces = $this->getInterfaces("Repositories/{$namespace}");

        foreach ($repositoryInterfaces as $interface) {
            $modelName = str_replace('RepositoryInterface', '', class_basename($interface));
            $concreteClass = "App\\Repositories\\{$namespace}\\{$modelName}Repository";

            if (class_exists($concreteClass)) {
                $this->app->bind(
                    "App\\Interfaces\\Repositories\\{$namespace}\\{$modelName}RepositoryInterface",
                    $concreteClass
                );
            }
        }
    }

    // Services bağlama işlemi
    foreach ($namespaces as $namespace) {
        $serviceInterfaces = $this->getInterfaces("Services/{$namespace}");

        foreach ($serviceInterfaces as $interface) {
            $modelName = str_replace('ServiceInterface', '', class_basename($interface));
            $concreteClass = "App\\Services\\{$namespace}\\{$modelName}Service";

            if (class_exists($concreteClass)) {
                $this->app->bind(
                    "App\\Interfaces\\Services\\{$namespace}\\{$modelName}ServiceInterface",
                    $concreteClass
                );
            }
        }
    }
}


    /**
     * Belirtilen interface klasöründeki tüm interface'leri al
     */
    private function getInterfaces(string $type): array
    {
        $path = app_path("Interfaces/{$type}");
        
        if (!File::isDirectory($path)) {
            return [];
        }

        $files = File::files($path);
        
        return array_map(function ($file) use ($type) {
            return 'App\\Interfaces\\' . $type . '\\' . pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }, $files);
    }

    public function boot(): void
    {
        //
    }
}