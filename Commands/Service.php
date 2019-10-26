<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;

class Service extends GeneratorCommand
{
    protected $service_directory;
    protected $repository_directory;
    protected $base_path;
    protected $files;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {service} {repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service class {service: service name} {repository: repository name}';

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
        $service_name = $this->argument('service');
        $this->repository_directory = config('repository.repository_directory');
        $this->service_directory = config('repository.service_directory');
        $this->base_path = base_path() . '/app/';
        $repository_path = $this->base_path . $this->repository_directory;
        $service_path = $this->base_path . $this->service_directory;
        $isRepositoryExist = $this->isFileExist($repository_path, $repository_name);
        $isServiceDirectory = $isRepositoryExist ? $this->isServiceDirectoryExist($service_path) : $this->repositoryError();

        if ($isServiceDirectory) {
            $this->makeServiceClass($service_path, $service_name, $repository_name);
            $this->makeAbstractClass();

        }
    }


    protected function makeServiceClass($path, $service, $repository): void
    {
        $filePath = $path . '/' . $service . '.php';
        if (!$this->files->exists($filePath)) {
            $this->files->put($filePath, $this->serviceContent($service, $repository));
            $this->info($service . ' Service has been created successfully');
        } else {
            $this->error($service . ' Service exists.');
        }
    }

    protected function makeAbstractClass(): void
    {
        $filePath = $this->base_path . $this->service_directory .'/AbstractCrudService.php';
        if (!$this->files->exists($filePath)) {
            $this->line('Abstract CRUD Service does not exist.');
            $this->files->put($filePath, $this->serviceAbstractContent());
            $this->info('Abstract CRUD Service has been created successfully');
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

    protected function repositoryError(): bool
    {
        $this->error("Repository file does not exist.");
        $this->info("Run 'php artisan make:repository {repository_name} {model_name} --s={service_name : optional}' to make repository file.");
        return false;
    }

    protected function isServiceDirectoryExist($path): bool
    {
        $isDirectoryExist = $this->files->isDirectory($path);
        if (!$isDirectoryExist) {
            $this->line($path . ' directory does not exist.');
            $this->files->makeDirectory($path, 0755, true, true);
            $this->info($path . ' directory created.');
            $isDirectoryExist = true;
        }
        return $isDirectoryExist;
    }

    protected function isFileExist(string $path, $fileName): bool
    {
        $path = $path . '/' . $fileName . '.php';
        return $this->files->exists($path);
    }

    protected function serviceAbstractContent()
    {
        return $content = '<?php
/**
 * Created by PhpStorm.
 * User: Towfiqul Islam
 * Email: towfiq.106@gmail.com
 * Date: ' . date('d-m-Y', time()) . '
 * Time: ' . date('h:i A', time()) . '
 */


namespace App\\' . $this->service_directory . ';


use App\\' . $this->repository_directory . '\AbstractRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCrudService
{
    /**
     * Instance that extends App\Repositories\AbstractBaseRepository;
     *
     * @var AbstractRepository
     */
    private $actionRepository;


    /**
     * @param AbstractRepository $actionRepository
     */
    public function setActionRepository(AbstractRepository $actionRepository): void
    {
        $this->actionRepository = $actionRepository;
    }


    /**
     * @param $id
     * @param null $relation
     * @return Model|null
     */
    public function findOne($id, $relation = null)
    {
        return $this->actionRepository->findOne($id, $relation);
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
        return $this->actionRepository->findIn($key, $values, $relation, $orderBy);
    }

    /**
     * @param null $perPage
     * @param null $relation
     * @param array|null $orderBy
     * @return LengthAwarePaginator|Builder[]|Collection|Model[]
     */
    public function findAll($perPage = null, $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findAll($perPage, $relation, $orderBy);
    }

    /**
     * @param array $searchCriteria
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */
    public function findBy(array $searchCriteria = [], $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findBy($searchCriteria, $relation, $orderBy);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model|mixed
     */
    public function update(Model $model, array $data)
    {
        return $this->actionRepository->update($model, $data);
    }


    /**
     * @param Model $model
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function delete(Model $model)
    {
        return $this->actionRepository->delete($model);
    }


    /**
     * @param $id
     * @param null $relation
     * @param array|null $orderBy
     * @return Builder|Builder[]|Collection|Model|Model[]|mixed
     */
    public function findOrFail($id, $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findOrFail($id);
    }


    /**
     * @param array $data
     * @return Model
     */
    public function save(array $data)
    {
        return $this->actionRepository->save($data);
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
        return $this->actionRepository->saveOrUpdate($attributes, $data);
    }

    /**
     * @param array $selectedColumns
     * @param null $relation
     * @param array|null $orderBy
     * @return Collection
     */

    public function findSelected(array $selectedColumns = [], $relation = null, array $orderBy = null)
    {
        return $this->actionRepository->findSelected($selectedColumns, $relation, $orderBy);
    }
}
';
    }

    protected function serviceContent($service, $repository)
    {
        $repositoryMember = $repository;
        $repositoryMember[0] = strtolower($repositoryMember[0]);
        return $content = '<?php


namespace App\\'.$this->service_directory.';


use App\\'.$this->repository_directory.'\\'.$repository.';

class '.$service.' extends AbstractCrudService
{
    private $'.$repositoryMember.';

    public function __construct('.$repository.' $'.$repositoryMember.')
    {
        $this->'.$repositoryMember.' = $'.$repositoryMember.';
        $this->setActionRepository($'.$repositoryMember.');
    }
}

';


    }

}

