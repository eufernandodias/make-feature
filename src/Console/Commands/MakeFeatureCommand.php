<?php

namespace Eufernandodias\MakeFeature\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFeatureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:feature {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new feature structure';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $featureName = $this->argument('name');

        if ($this->featureExists($featureName)) {
            $this->error("Feature {$featureName} already exists!");
            return 1;
        }

        $this->info("Creating directories for feature {$featureName}...");
        $this->createDirectories($featureName);

        $this->info("Creating files for feature {$featureName}...");
        $this->createFiles($featureName);

        $featureDir = app_path("Features/{$featureName}");

        $this->info("Creating tests for feature {$featureName}...");
        $this->createControllerTestFile($featureDir, $featureName);
        $this->createModelTestFile($featureDir, $featureName);

        $this->info("Feature {$featureName} created successfully!");

        return 0;
    }

    /**
     * Check if a feature already exists.
     *
     * @param string $featureName The name of the feature.
     * @return bool True if the feature exists, false otherwise.
     */
    private function featureExists($featureName)
    {
        $featureDir = app_path("Features/{$featureName}");
        return File::exists($featureDir);
    }

    /**
     * Create the directories for the feature.
     *
     * @param string $featureName The name of the feature.
     * @return void
     */
    private function createDirectories($featureName)
    {
        $featureDir = app_path("Features/{$featureName}");

        $directories = ["Controllers", "Requests", "Services", "Repositories", "Models", "Tests"];
        foreach ($directories as $directory) {
            $this->info("Creating directory: {$featureDir}/{$directory}");
            File::makeDirectory("{$featureDir}/{$directory}", 0755, true);
        }
    }

    /**
     * Create the files for the feature.
     *
     * @param string $featureName The name of the feature.
     * @return void
     */
    private function createFiles($featureName)
    {
        $featureDir = app_path("Features/{$featureName}");

        $controllerType = $this->choice('What type of controller would you like to create?', ['Resource', 'Singleton', 'API', 'Invokable', 'Empty'], 0);

        $this->createControllerFile($featureDir, $controllerType, $featureName);
        $this->createModelFile($featureDir, $featureName);
        $this->createRepositoryFile($featureDir, $featureName);
        $this->createServiceFile($featureDir, $featureName);
        $this->createRequestFiles($featureDir, $featureName);
        $this->createTestFile($featureDir, $controllerType, $featureName);
        $this->createRepositoryInterfaceFile($featureDir, $featureName);
        $this->createServiceInterfaceFile($featureDir, $featureName);
    }

    /**
     * Create the controller file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $controllerType The type of the controller.
     * @param string $featureName The name of the feature.
     */
    private function createControllerFile($featureDir, $controllerType, $featureName)
    {
        $this->info("Creating controller file: {$featureDir}/Controllers/{$featureName}Controller.php");
        File::put("{$featureDir}/Controllers/{$featureName}Controller.php", $this->getControllerContent($controllerType, $featureName));
    }

    /**
     * Create the model file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createModelFile($featureDir, $featureName)
    {
        $this->info("Creating model file: {$featureDir}/Models/{$featureName}.php");
        File::put("{$featureDir}/Models/{$featureName}.php", $this->getModelContent($featureName));
    }

    /**
     * Create the repository file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createRepositoryFile($featureDir, $featureName)
    {
        $this->info("Creating repository file: {$featureDir}/Repositories/{$featureName}Repository.php");
        File::put("{$featureDir}/Repositories/{$featureName}Repository.php", $this->getRepositoryContent($featureName));
    }

    /**
     * Create the service file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createServiceFile($featureDir, $featureName)
    {
        $this->info("Creating service file: {$featureDir}/Services/{$featureName}Service.php");
        File::put("{$featureDir}/Services/{$featureName}Service.php", $this->getServiceContent($featureName));
    }

    /**
     * Create the request files for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createRequestFiles($featureDir, $featureName)
    {
        $this->info("Creating request files: {$featureDir}/Requests/{$featureName}StoreRequest.php and {$featureDir}/Requests/{$featureName}UpdateRequest.php");
        File::put("{$featureDir}/Requests/{$featureName}StoreRequest.php", $this->getRequestContent($featureName, 'Store'));
        File::put("{$featureDir}/Requests/{$featureName}UpdateRequest.php", $this->getRequestContent($featureName, 'Update'));
    }

    /**
     * Create the test file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $controllerType The type of the controller.
     * @param string $featureName The name of the feature.
     */
    private function createTestFile($featureDir, $controllerType, $featureName)
    {
        $this->info("Creating test file: {$featureDir}/Tests/{$featureName}Test.php");
        File::put("{$featureDir}/Tests/{$featureName}Test.php", $this->getTestContent($featureName, $controllerType));
    }

    /**
     * Create the repository interface file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createRepositoryInterfaceFile($featureDir, $featureName)
    {
        $this->info("Creating repository interface file: {$featureDir}/Repositories/{$featureName}RepositoryInterface.php");
        File::put("{$featureDir}/Repositories/{$featureName}RepositoryInterface.php", $this->getRepositoryInterfaceContent($featureName));
    }

    /**
     * Create the service interface file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createServiceInterfaceFile($featureDir, $featureName)
    {
        $this->info("Creating service interface file: {$featureDir}/Services/{$featureName}ServiceInterface.php");
        File::put("{$featureDir}/Services/{$featureName}ServiceInterface.php", $this->getServiceInterfaceContent($featureName));
    }

    /**
     * Retrieves the content of a controller based on the controller type and feature name.
     *
     * @param string $controllerType The type of the controller ('Resource', 'API', 'Singleton', 'Invokable', 'Empty').
     * @param string $featureName The name of the feature.
     * @return string The content of the controller.
     */
    private function getControllerContent($controllerType, $featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Controllers";
        $className = "{$featureName}Controller";
        $namespacedModel = "App\\Features\\{$featureName}\\Models\\{$featureName}";
        $rootNamespace = "App\\";
        $storeRequest = "{$featureName}StoreRequest";
        $updateRequest = "{$featureName}UpdateRequest";
        $service = "{$featureName}Service";
        $model = $featureName;
        $modelVariable = lcfirst($featureName);

        // Determine the stub file based on the controller type
        $stubFile = match ($controllerType) {
            'Resource' => 'controller.model.api.stub',
            'API' => 'controller.api.stub',
            'Singleton' => 'controller.singleton.api.stub',
            'Invokable' => 'controller.invokable.stub',
            'Empty' => 'controller.plain.stub',
        };

        // Load the content of the stub file
        $stubContent = File::get(base_path("stubs/features/controllers/{$stubFile}"));

        // Replace the placeholders with the class name and namespace
        $content = str_replace(
            ['{{ namespace }}', '{{ namespacedModel }}', '{{ rootNamespace }}', '{{ class }}', '{{ storeRequest }}', '{{ updateRequest }}', '{{ service }}', '{{ model }}', '{{ modelVariable }}', '{{ featureName }}'],
            [$namespace, $namespacedModel, $rootNamespace, $className, $storeRequest, $updateRequest, $service, $model, $modelVariable, $featureName],
            $stubContent
        );

        return $content;
    }

    /**
     * Retrieves the content of a model based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the model.
     */
    private function getModelContent($featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Models";
        $className = $featureName;

        $content = "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Database\\Eloquent\\Factories\\HasFactory;\nuse Illuminate\\Database\\Eloquent\\Model;\n\n";
        $content .= "class {$className} extends Model\n{\n    use HasFactory;\n}\n";

        return $content;
    }

    /**
     * Retrieves the content of a repository based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the repository.
     */
    private function getRepositoryContent($featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Repositories";
        $className = "{$featureName}Repository";
        $model = "App\\Features\\{$featureName}\\Models\\{$featureName}";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse App\\Repositories\\BaseRepository;\nuse {$model};\n\n";
        $content .= "class {$className} extends BaseRepository\n{\n    public function __construct({$featureName} \$model)\n    {\n        parent::__construct(\$model);\n    }\n}\n";

        return $content;
    }

    /**
     * Retrieves the content of a repository interface based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the repository interface.
     */
    private function getRepositoryInterfaceContent($featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Repositories";
        $className = "{$featureName}RepositoryInterface";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse App\\Repositories\\BaseRepositoryInterface;\n\n";
        $content .= "interface {$className} extends BaseRepositoryInterface\n{\n    // Adicione aqui os métodos específicos para {$className}\n}\n";

        return $content;
    }

    /**
     * Retrieves the content of a service interface based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the service interface.
     */
    private function getServiceInterfaceContent($featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Services";
        $className = "{$featureName}ServiceInterface";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse App\\Services\\Layers\\BaseServiceInterface;\n\n";
        $content .= "interface {$className} extends BaseServiceInterface\n{\n    // Adicione aqui os métodos específicos para {$className}\n}\n";

        return $content;
    }

    /**
     * Retrieves the content of a service based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the service.
     */
    private function getServiceContent($featureName)
    {
        $namespace = "App\\Features\\{$featureName}\\Services";
        $className = "{$featureName}Service";
        $repository = "App\\Features\\{$featureName}\\Repositories\\{$featureName}Repository";
        $baseService = "App\\Services\\Layers\\BaseService";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse {$repository};\nuse {$baseService};\n\n";
        $content .= "class {$className} extends BaseService\n{\n    public function __construct({$featureName}Repository \$repository)\n    {\n        parent::__construct(\$repository);\n    }\n}\n";

        return $content;
    }


    /**
     * Retrieves the content of a request based on the feature name and request type.
     *
     * @param string $featureName The name of the feature.
     * @param string $requestType The type of the request ('Store', 'Update').
     * @return string The content of the request.
     */
    private function getRequestContent($featureName, $requestType)
    {
        $namespace = "App\\Features\\{$featureName}\\Requests";
        $className = "{$featureName}{$requestType}Request";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\n";
        $content .= "class {$className} extends FormRequest\n{\n    public function authorize()\n    {\n        return true;\n    }\n\n    public function rules()\n    {\n        return [\n            // Defina aqui as regras de validação para a criação de um perfil\n        ];\n    }\n}\n";

        return $content;
    }

    /**
     * Retrieves the content of a test based on the feature name and controller type.
     *
     * @param string $featureName The name of the feature.
     * @param string $controllerType The type of the controller ('Resource', 'API', 'Singleton', 'Invokable', 'Empty').
     * @return string The content of the test.
     */
    private function getTestContent($featureName, $controllerType)
    {
        $namespace = "Tests\\Feature\\{$featureName}";
        $className = "{$featureName}Test";
        $namespacedModel = "App\\Features\\{$featureName}\\Models\\{$featureName}";
        $modelVariable = lcfirst($featureName);

        $content = "<?php\n\nnamespace {$namespace};\n\nuse {$namespacedModel};\nuse Tests\\TestCase;\n\n";
        $content .= "class {$className} extends TestCase\n{\n";

        if ($controllerType == 'Resource') {
            $content .= "    /**\n     * Test the index method.\n     *\n     * @return void\n     */\n    public function testIndex()\n    {\n        // TODO: Implement test\n    }\n\n";
            $content .= "    /**\n     * Test the store method.\n     *\n     * @return void\n     */\n    public function testStore()\n    {\n        // TODO: Implement test\n    }\n\n";
            $content .= "    /**\n     * Test the show method.\n     *\n     * @return void\n     */\n    public function testShow()\n    {\n        // TODO: Implement test\n    }\n\n";
            $content .= "    /**\n     * Test the update method.\n     *\n     * @return void\n     */\n    public function testUpdate()\n    {\n        // TODO: Implement test\n    }\n\n";
            $content .= "    /**\n     * Test the destroy method.\n     *\n     * @return void\n     */\n    public function testDestroy()\n    {\n        // TODO: Implement test\n    }\n";
        } else {
            $content .= "    /**\n     * A basic test example.\n     *\n     * @return void\n     */\n    public function testExample()\n    {\n        \$this->assertTrue(true);\n    }\n";
        }

        $content .= "}\n";

        return $content;
    }

    /**
     * Create the controller test file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createControllerTestFile($featureDir, $featureName)
    {
        $this->info("Creating controller test file: {$featureDir}/Tests/{$featureName}ControllerTest.php");
        File::put("{$featureDir}/Tests/{$featureName}ControllerTest.php", $this->getControllerTestContent($featureName));
    }

    /**
     * Create the model test file for a feature.
     *
     * @param string $featureDir The directory of the feature.
     * @param string $featureName The name of the feature.
     */
    private function createModelTestFile($featureDir, $featureName)
    {
        $this->info("Creating model test file: {$featureDir}/Tests/{$featureName}Test.php");
        File::put("{$featureDir}/Tests/{$featureName}Test.php", $this->getModelTestContent($featureName));
    }

    /**
     * Retrieves the content of a controller test based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the controller test.
     */
    private function getControllerTestContent($featureName)
    {
        $namespace = "Tests\\Feature\\{$featureName}";
        $className = "{$featureName}ControllerTest";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse Tests\\TestCase;\n\n";
        $content .= "class {$className} extends TestCase\n{\n";
        $content .= "    /**\n     * A basic test example.\n     *\n     * @return void\n     */\n    public function testExample()\n    {\n        \$this->assertTrue(true);\n    }\n";
        $content .= "}\n";

        return $content;
    }

    /**
     * Retrieves the content of a model test based on the feature name.
     *
     * @param string $featureName The name of the feature.
     * @return string The content of the model test.
     */
    private function getModelTestContent($featureName)
    {
        $namespace = "Tests\\Feature\\{$featureName}";
        $className = "{$featureName}Test";

        $content = "<?php\n\nnamespace {$namespace};\n\nuse Tests\\TestCase;\n\n";
        $content .= "class {$className} extends TestCase\n{\n";
        $content .= "    /**\n     * A basic test example.\n     *\n     * @return void\n     */\n    public function testExample()\n    {\n        \$this->assertTrue(true);\n    }\n";
        $content .= "}\n";

        return $content;
    }
}
