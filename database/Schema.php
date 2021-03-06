<?php

namespace Sagi\Database;

use Closure;

class Schema
{

    /**
     * @var array
     *
     */
    protected $patterns = [
        'create' => 'CREATE TABLE `%s`(',
        'end' => ') DEFAULT CHARSET=%s;',
        'drop' => 'DROP TABLE %s;',
        'create_if' => 'CREATE TABLE IF NOT EXISTS `%s`(',
    ];

    /**
     * @var Row
     */
    protected $row;

    /**
     * @var array
     */
    protected $commands;

    /**
     * @var string
     */
    protected $charset = 'utf8';

    /**
     * Schema constructor.
     */
    public function __construct()
    {
        $this->row = new Row();
    }

    /**
     * @param string $charset
     * @return $this
     */
    public function charset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @param string $table
     * @param Closure $closure
     * @return $this
     */
    public function createTable($table, Closure $closure)
    {
        $this->addCommand('create', [$table]);

        call_user_func_array($closure, [$this->row->setTable($table)]);

        $this->commands[] = $this->row->prepareRow();
        $this->addCommand('end', [$this->charset]);

        $prepare = QueryBuilder::createNewInstance()->prepare($query = $this->prepareSchema(), []);
        $this->checkResult($prepare, $query);
    }

    /**
     * @param string $table
     * @param Closure $closure
     * @return $this
     */
    public function createTableIfNotExists($table, Closure $closure)
    {
        $this->addCommand('create_if', [$table]);

        call_user_func_array($closure, [$this->row->setTable($table)]);

        $this->commands[] = $this->row->prepareRow();
        $this->addCommand('end', [$this->charset]);
        $prepare = QueryBuilder::createNewInstance()->prepare($query = $this->prepareSchema(), []);
        $this->checkResult($prepare, $query);
    }
    /**
     * @param \PDOStatement $prepare
     * @return bool
     * @throws SchemaException
     */
    private function checkResult($prepare, $query = '')
    {
        if ($prepare) {
            return true;
        } else {
            throw new SchemaException(json_encode(Connector::getConnection()->errorInfo())." On Query : $query");
        }
    }

    /**
     * @param string $table
     * @return mixed
     */
    public function dropTable($table)
    {
        $this->addCommand('drop', [$table]);

        $builder = new QueryBuilder();

        return $builder->prepare($this->prepareSchema(), []);
    }

    /**
     * @param $type
     * @param $variables
     * @return Schema
     */
    private function addCommand($type, $variables)
    {
        if (!empty($variables)) {
            array_unshift($variables, $this->patterns[$type]);

            $command = call_user_func_array('sprintf', $variables);
        } else {
            $command = $this->patterns[$type];
        }

        $this->commands[] = $command;

        return $this;
    }

    /**
     * @return string
     */
    public function prepareSchema()
    {
        return join('', $this->commands);
    }
}
