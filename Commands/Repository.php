<?php

/**
 * Created by PhpStorm.
 * User: Towfiqul Islam
 * Email: towfiq.106@gmail.com
 * Date: 26-10-2019
 * Time: 02:40 AM
 */

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class Repository extends GeneratorCommand
{
    protected $base_path;
    protected $repository_directory;
    protected $model_directory;
    protected $path;

    protected $files;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {repository} {model} {--s|service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository class {repository: repository name} {model: model name} {--s|service: service name}';

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $repository_name = $this->argument('repository');
        $model = $this->argument('model');
        $service = $this->option('service');
        $this->repository_directory = config('repository.repository_directory');
        $this->model_directory = config('repository.model_directory');
        $this->base_path = base_path() . '/app/';
        $this->path = $this->base_path . $this->repository_directory;
        $isRepositoryDirectoryExist = $this->isRepositoryDirectoryExist($this->path);
        $isContractsDirectoryExist = $isRepositoryDirectoryExist ? $this->isContractsDirectoryExist($this->path) : false;
        $isModelExist = $this->isFileExist($this->base_path . $this->model_directory, $model);
        if (!$isModelExist) {
            $this->error("Model not found");
        }
        if ($isRepositoryDirectoryExist && $isModelExist) {
            if ($isContractsDirectoryExist) {
                $this->makeInterface();
            } else {
                $this->error('Contracts Directory does not exits.');
                $this->error('Interface class creation failed.');
                $this->error($repository_name . ' Repository class creation failed.');
            }
            $this->makeAbstractClass();
            $this->makeRepositoryClass($repository_name, $model);
        } else {
            $this->error($repository_name . ' Repository class creation failed.');
        }
        if($service){
            $this->makeService($service, $repository_name);
        }


    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        //TODO::
    }

    protected function isFileExist(string $path, $fileName): bool
    {
        $path = $path . '/' . $fileName . '.php';
        return $this->files->exists($path);
    }

    protected function makeService($service, $repository)
    {
        $this->call('make:service', [
            'service' => $service, 'repository' => $repository
        ]);
    }

    /**
     * @param string $path
     * @return bool
     */


    protected function isRepositoryDirectoryExist(string $path): bool
    {
        $isDirectoryExist = $this->files->isDirectory($path);
        if (!$isDirectoryExist) {
            if ($this->confirm('Repositories directory does not exist. Shall I create directory ' . $this->repository_directory . ' ?')) {
                $this->path = $this->base_path . $this->repository_directory;
                $this->files->makeDirectory($this->path, 0755, true, true);
                $this->info($this->path . ' directory created.');
                $isDirectoryExist = true;
            }
        }
        return $isDirectoryExist;
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isContractsDirectoryExist($path): bool
    {
        $path = $path . '/Contracts';
        $isDirectoryExist = $this->files->isDirectory($path);
        if (!$isDirectoryExist) {
            $this->line($path . ' directory does not exist.');
            $this->files->makeDirectory($path, 0755, true, true);
            $this->info($path . ' directory created.');
            $isDirectoryExist = true;
        }
        return $isDirectoryExist;
    }

    protected function repositoryInterfaceContent()
    {
        return $content = '<?php
/**
 * Created by PhpStorm.
 * User: Towfiqul Islam
 * Email: towfiq.106@gmail.com
 * Date: ' . date('d-m-Y', time()) . '
 * Time: ' . date('h:i A', time()) . '
 */

namespace App\\' . $this->repository_directory . '\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * Interface RepositoryInterface
 * @package App\Repositories\Contracts
 */
interface RepositoryInterface
{
    /**
     * Find a resource by id
     *
     * @param $id
     * @param $relation
     * @return Model|null
     */
    public function findOne($id, $relation);

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @param $relation
     * @return Model|null
     */
    public function findOneBy(array $criteria, $relation);

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findBy(array $searchCriteria = [], $relation = null, array $orderBy = null);

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findIn($key, array $values, $relation = null, array $orderBy = null);

    /**
     * @param null $perPage
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findAll($perPage = null, $relation = null, array $orderBy = null);

    /**
     * @param $id
     * @param null $relation
     * @param array|null $orderBy
     * @return mixed
     */
    public function findOrFail($id, $relation = null, array $orderBy = null);

    /**
     * Save a resource
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data);

    /**
     * Update a resource
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data);

    /**
     * Save or Update a resource
     *
     * @param array $attributes
     * @param array $data
     * @return Model
     */
    public function saveOrUpdate(array $attributes, array $data);

    /**
     * Delete a resource
     *
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model);
}
';
    }


    /**
     * @return string
     */

    protected function repositoryAbstractContent()
    {
        return $content = '<?php
/**
 * Created by PhpStorm.
 * User: Towfiqul Islam
 * Email: towfiq.106@gmail.com
 * Date: ' . date('d-m-Y', time()) . '
 * Time: ' . date('h:i A', time()) . '
 */

namespace App\\' . $this->repository_directory . ';


use App\\' . $this->repository_directory . '\Contracts\RepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


/**
 * Class AbstractBaseRepository
 * @package App\Repositories
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     * @throws Exception
     */
    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Instantiate Model
     *
     * @throws Exception
     */
    public function setModel()
    {
        //check if the class exists
        if (class_exists($this->modelName)) {
            $this->model = new $this->modelName;

            //check object is a instanceof Illuminate\Database\Eloquent\Model
            if (!$this->model instanceof Model) {
                throw new Exception("{$this->modelName} must be an instance of Illuminate\Database\Eloquent\Model");
            }

        } else {
            throw new Exception(\'No model name defined\');
        }
    }

    /**
     * Get Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Find a resource by id
     *
     * @param $id
     * @param null $relation
     * @return Model|null
     */
    public function findOne($id, $relation = null)
    {
        return $this->findOneBy([\'id\' => $id], $relation);
    }

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @param null $relation
     * @return Model|null
     */
    public function findOneBy(array $criteria, $relation = null)
    {
        return $this->prepareModelForRelationAndOrder($relation)->where($criteria)->first();
    }

    /**
     * Search All resources by criteria
     *
     * @param array $searchCriteria
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findBy(array $searchCriteria = [], $relation = null, array $orderBy = null)
    {
        $model = $this->prepareModelForRelationAndOrder($relation, $orderBy);
        $limit = !empty($searchCriteria[\'per_page\']) ? (int)$searchCriteria[\'per_page\'] : 15; // it\'s needed for pagination

        $queryBuilder = $model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        }
        );
        if (!empty($searchCriteria[\'per_page\'])) {
            return $queryBuilder->paginate($limit);
        }
        return $queryBuilder->get();
    }

    /**
     * Find the Selected Columns
     *
     * @param array $selectedColumns
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findSelected(array $selectedColumns = [], $relation = null, array $orderBy = null)
    {
        $model = $this->prepareModelForRelationAndOrder($relation, $orderBy);
        $queryBuilder = $model->select($selectedColumns);

        return $queryBuilder->get();
    }

    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, [\'page\', \'per_page\'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(\',\', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } else {
                $operator = \'=\';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findIn($key, array $values, $relation = null, array $orderBy = null)
    {
        return $this->prepareModelForRelationAndOrder($relation, $orderBy)->whereIn($key, $values)->get();
    }


    /**
     * @param null $perPage
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection|LengthAwarePaginator|Builder[]|Collection|Model[]
     */
    public function findAll($perPage = null, $relation = null, array $orderBy = null)
    {
        $model = $this->prepareModelForRelationAndOrder($relation, $orderBy);
        if ($perPage) {
            return $model->paginate($perPage);
        }

        return $model->get();
    }

    /**
     * @param $id
     * @param null $relation
     * @param array|null $orderBy
     * @return Builder|Builder[]|Collection|Model|Model[]|mixed
     */
    public function findOrFail($id, $relation = null, array $orderBy = null)
    {
        return $this->prepareModelForRelationAndOrder($relation, $orderBy)->findOrFail($id);
    }


    /**
     * Save a resource
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a resource
     *
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {
            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // update the model
        return $model->save();
    }

    /**
     * Save or Update a resource
     *
     * @param array $attributes
     * @param array $data
     * @return Model
     */
    public function saveOrUpdate(array $attributes, array $data)
    {
        return $this->model->updateOrCreate($attributes, $data);
    }

    /**
     * Delete a resource
     *
     * @param Model $model
     * @return mixed
     * @throws Exception
     */
    public function delete(Model $model)
    {
        return $model->delete();
    }

    /**
     * @param $relation
     * @param array|null $orderBy [[Column], [Direction]]
     * @return Builder|Model
     */
    private function prepareModelForRelationAndOrder($relation, array $orderBy = null)
    {
        $model = $this->model;
        if ($relation) {
            $model = $model->with($relation);
        }
        if ($orderBy) {
            $model = $model->orderBy($orderBy[\'column\'], $orderBy[\'direction\']);
        }
        return $model;
    }
}
';
    }


    /**
     * @param bool $isContractsDirectoryExist
     * @param $repository_name
     */


    protected function makeInterface(): void
    {
        $filePath = $this->path . '/Contracts/RepositoryInterface.php';
        if (!$this->files->exists($filePath)) {
            $this->line('Repository Interface does not exist.');
            $this->files->put($filePath, $this->repositoryInterfaceContent());
            $this->info('Repository Interface has been created successfully');
        }
    }


    /**
     * @param $repository_name
     */

    protected function makeAbstractClass(): void
    {
        $filePath = $this->path . '/AbstractRepository.php';
        if (!$this->files->exists($filePath)) {
            $this->line('Abstract Repository does not exist.');
            $this->files->put($filePath, $this->repositoryAbstractContent());
            $this->info('Abstract Repository has been created successfully');
        }
    }


    /**
     * @param $repository_name
     * @param $model
     */

    protected function makeRepositoryClass($repository_name, $model): void
    {
        $filePath = $this->path . '/' . $repository_name . '.php';
        if (!$this->files->exists($filePath)) {
            $this->files->put($filePath, $this->repositoryContent($repository_name, $model));
            $this->info($repository_name . ' Repository has been created successfully');
        } else {
            $this->error($repository_name . ' Repository exists.');
        }
    }


    /**
     * @param $name
     * @param $model
     * @return string
     */

    protected function repositoryContent($name, $model)
    {
        return $content = '<?php


namespace App\\' . $this->repository_directory . ';


use App\\' . config('repository.model_directory') . '\\' . $model . ';

class ' . $name . ' extends AbstractRepository
{
    protected $modelName = ' . $model . '::class;
}
';
    }

}

