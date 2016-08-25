<?php
namespace Sagi\Database;

use Iterator;
use PDO;

/**
 * Class QueryBuilder
 */
class QueryBuilder extends Engine implements Iterator
{


    /**
     * @var array
     */
    public $attributes;

    /**
     * QueryBuilder constructor.
     * @param array|null $configs
     * @param null|string $table
     */
    public function __construct($configs = null, $table = null)
    {
        parent::__construct($configs, $table);
    }

    /**
     * @return array
     */
    public function error()
    {
        return $this->pdo->errorInfo();
    }

    /**
     * @param $query
     * @param bool $ex
     * @return PDOStatement
     */
    private function returnPreparedResults($query, $ex = false)
    {
        $query = trim($query);
        $prepared = $this->pdo->prepare($query);

        $this->setArgs(array_slice($this->getArgs(), 0, count($this->getArgs())));

        $result = $prepared->execute($this->getArgs());

        if ($ex) {
            return $result;
        } else {
            return $prepared;
        }


    }


    /**
     * @return mixed
     */
    public function first()
    {
        return $this->attributes;
    }


    /**
     * @param $table
     * @return static
     */
    public static function createNewInstance()
    {
        return new static();
    }


    /**
     * @return QueryBuilder
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = static::createNewInstance();
        }

        return static::$instance;
    }

    /**
     * @return mixed
     */
    public function one()
    {
        $get = $this->get();

        return $get->fetchObject('Sagi\Database\Results', ['table' => $this->getTable(), 'database' => static::createNewInstance()]);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->get()->fetchAll(PDO::FETCH_CLASS, 'Sagi\Database\Results', ['table' => $this->getTable(), 'database' => static::createNewInstance()]);
    }


    /**
     * @return PDOStatement
     */
    public function get()
    {
        $handled = $this->prepareGetQuery();


        return $this->returnPreparedResults($handled);
    }

    /**
     * @return PDOStatement
     */
    public function delete()
    {
        return $this->returnPreparedResults($this->prepareDelete(), true);

    }

    /**
     * @param array $sets
     * @return PDOStatement
     */
    public function update($sets = [])
    {
        return $this->returnPreparedResults($this->prepareUpdate($sets), true);
    }

    /**
     * @param array $sets
     * @return PDOStatement
     */
    public function create($sets = [])
    {
        return $this->returnPreparedResults($this->prepareCreate($sets), true);
    }


    /**
     * @return int
     */
    public function count()
    {
        $handled = $this->returnPreparedResults($this->prepareGetQuery())->rowCount();

        return $handled;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return ($this->count() > 0);
    }



    public function rewind()
    {
        if (is_null($this->attributes)) {
            if (is_null($this->getLimit())) {
                $this->all();
            } elseif ($this->getLimit()[0] === 1) {
                $this->one();
            }

        }
        reset($this->attributes);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        $var = current($this->attributes);
        return $var;
    }

    /**
     * @return mixed
     */
    public function key()
    {

        $var = key($this->attributes);
        return $var;
    }

    /**
     * @return mixed
     */
    public function next()
    {
        $var = next($this->attributes);
        return $var;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $key = key($this->attributes);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }


    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->first()->$name;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return QueryBuilder
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }


}