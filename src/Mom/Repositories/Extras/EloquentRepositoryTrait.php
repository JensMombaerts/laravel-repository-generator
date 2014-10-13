<?php namespace Mom\Repositories\Extras;

trait EloquentRepositoryTrait {

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var null
     */
    protected $modelName = null;

    /**
     * @throws Exception
     */
    public function __construct() {
        $this->checkIfModelNameIsSet();
        $this->checkIfClassExists();
        $this->model = new $this->modelName;
        $this->checkIfObjectIsInstanceOfModel();
    }

    /**
     * Return all users
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function make(array $with = array())
    {
        return $this->model->with($with);
    }

    /**
     * @param $id
     * @param array $with
     * @return Model|null|static
     */
    public function getById($id, array $with = array())
    {
        return $this->make($with)->find($id);
    }

    /**
     * @param $key
     * @param $value
     * @param array $with
     * @return Model|null|static
     */
    public function getFirstBy($key, $value, array $with = array())
    {
        return $this->make($with)->where($key, '=', $value)->first();
    }

    /**
     * @param $key
     * @param $value
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getManyBy($key, $value, array $with = array())
    {
        return $this->make($with)->where($key, '=', $value)->get();
    }

    /**
     * @param $relation
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function has($relation, array $with = array())
    {
        return $this->make($with)->has($relation)->get();
    }

    /**
     * @throws Exception
     */
    private function checkIfModelNameIsSet()
    {
        if ($this->modelName === null) {
            throw new Exception("No \$modelName set.");
        }
    }

    /**
     * @throws Exception
     */
    private function checkIfClassExists()
    {
        if (!class_exists($this->modelName)) {
            throw new Exception("Class does not exist [{$this->modelName}].");
        }
    }

    /**
     * @throws Exception
     */
    private function checkIfObjectIsInstanceOfModel()
    {
        if (!$this->model instanceof Model) {
            throw new Exception("Class [{$this->modelName}] is not an instance of Model.");
        }
    }

} 