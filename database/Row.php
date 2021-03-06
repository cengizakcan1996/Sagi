<?php
/**
 * Created by PhpStorm.
 * User: sagi
 * Date: 24.08.2016
 * Time: 15:05
 */

namespace Sagi\Database;


class Row
{

    protected $patterns = [
        'int' => '`%s` INT(%d)',
        'bigint' => '`%s` BIGINT(%d)',
        'tinyint' => '`%s` TINYINT(%d)',
        'varchar' => '`%s` VARCHAR(%d)',
        'timestamp' => '`%s` TIMESTAMP',
        'current' => '`%s` TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'date' => '`%s` DATE',
        'year' => '`%s` YEAR',
        'time' => '`%s` TIME',
        'datetime' => '`%s` DATETIME',
        'text' => '`%s` TEXT',
        'float' => '`%s` FLOAT(%d)',
        'decimal' => '`%s` DECIMAL(%d,%d)',
        'bool' => '`%s` BOOLEAN',
        'bit' => '`%s` BIT',
        'char' => '`%s` CHAR(%d)',
        'primary_key' => 'PRIMARY KEY(%s)',
        'foreign_key' => 'FOREIGN KEY(`%s`) REFERENCES `%s`(`%s`)',
        'index' => 'INDEX `%s` (`%s`) ',
        'fulltext' => 'FULLTEXT %s (%s)'
    ];

    /**
     * @var array
     */
    protected $subPatterns = [
        'string' => ':command :default :null',
        'integer' => ':command :signed :null :unique :increment',
        'other' => ':substring :command :end'
    ];

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $table
     * @return Row
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }



    public function index($name, $col)
    {
        return $this->addCommand('index', [$name, $col], 'other');
    }

    /**
     * @var array
     */
    protected static $sqlCommands;

    /**
     * add a text string to value
     *
     * @param string $name
     * @return Command
     */
    public function text($name)
    {
        return $this->addCommand('text', $this->madeArray($name), 'string');
    }

    /**
     * add a new varchar command
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function string($name, $limit = 255)
    {
        return $this->addCommand('varchar', $this->madeArray($name, $limit), 'string');
    }

    /**
     * add a new char command
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function char($name, $limit = 255)
    {
        return $this->addCommand('char', $this->madeArray($name, $limit), 'string');
    }

    /**
     * add a new date command
     *
     * @param  string $name
     * @return Command
     */
    public function date($name)
    {
        return $this->addCommand('date', $this->madeArray($name), 'string');
    }

    /**
     * add a new integer command
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function int($name, $limit = 255)
    {
        return $this->addCommand('int', $this->madeArray($name, $limit), 'integer');
    }

    /**
     * add a new integer command
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function tinyInt($name, $limit = 255)
    {
        return $this->addCommand('tinyint', $this->madeArray($name, $limit), 'integer');
    }

    /**
     * add a new integer command
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function bigInt($name, $limit = 255)
    {
        return $this->addCommand('bigint', $this->madeArray($name, $limit), 'integer');
    }

    /**
     * add a new time string
     *
     * @param string $name
     * @return Command
     */
    public function time($name)
    {
        return $this->addCommand('time', $this->madeArray($name), 'integer');
    }

    /**
     * add a new time string
     *
     * @param string $name
     * @return Command
     */
    public function bool($name)
    {
        return $this->addCommand('bool', $this->madeArray($name), 'integer');
    }


    /**
     * add a new time string
     *
     * @param string $name
     * @return Command
     */
    public function bit($name)
    {
        return $this->addCommand('bit', $this->madeArray($name), 'string');
    }

    /**
     * add a new timestamp column to mysql
     *
     * @param string $name
     * @return Command
     */
    public function timestamp($name)
    {
        return $this->addCommand('timestamp', $this->madeArray($name), 'string');
    }

    /**
     * add a new year year column to mysql
     *
     * @param string $name
     * @return Command
     */
    public function year($name)
    {
        return $this->addCommand('year', $this->madeArray($name), 'string');
    }

    /**
     * add a new auto_increment column to mysql
     *
     * @param string $name
     * @param int $limit
     * @return Command
     */
    public function pk($name, $limit = 255)
    {
        return $this->addCommand('int', $this->madeArray($name, $limit),
            'integer')->unsigned()->notNull()->autoIncrement();
    }

    /**
     * @return $this
     */
    public function timestamps()
    {
        $this->current(Model::CREATED_AT);
        $this->timestamp(Model::UPDATED_AT)->null();

        return $this;
    }

    /**
     * add a new time stamp with CURRENT_TIMESTAMP
     *
     * @param string $name
     * @return Command
     */
    public function current($name)
    {
        return $this->addCommand('current', $this->madeArray($name), 'string');
    }

    /**
     * @return Command
     */
    public function auth()
    {
        return $this->string('role')->defaultValue('user');
    }

    /**
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return Command
     */
    public function decimal($name, $precision, $scale)
    {
        return $this->addCommand('decimal', [$name, $precision, $scale], 'integer');
    }

    /**
     * @param string $name
     * @param int $precision
     * @return Command
     */
    public function float($name, $precision)
    {
        return $this->addCommand('float', [$name, $precision], 'integer');
    }

    /**
     * @param $keys
     * @return Command
     */
    public function primaryKey($keys)
    {
        if (is_string($keys)) {
            $keys = array_map(function ($value){
                return "`$value`";
            }, explode(',', $keys));
        }

        $keys = join(',', $keys);

        return $this->addCommand('primary_key', [$keys], 'other');
    }

    /**
     * @param $keys
     * @return Command
     */
    public function foreignKey($table, $colOur, $colTarget)
    {


        return $this->addCommand('foreign_key', [$colOur, $table, $colTarget], 'other');
    }


    /**
     * @param $index
     * @param $columns
     * @return Command
     */
    public function fulltext($index, $columns){
        if (is_array($columns)) {
            $columns = join(',', $columns);
        }

        return $this->addCommand('fulltext', [$index, $columns], 'other');
    }

    /**
     * @param $table
     * @param $ourCol
     * @param $tarCol
     * @return Row
     */
    public function makeOneRelation($table, $ourCol, $tarCol)
    {
        return $this->makeRelation('one', $table, $ourCol, $tarCol);
    }

    /**
     * @param $table
     * @param $ourCol
     * @param $tarCol
     * @return Row
     */
    public function makeManyRelation($table, $ourCol, $tarCol)
    {
        return $this->makeRelation('many', $table, $ourCol, $tarCol);
    }

    /**
     * @param string $type
     * @param $table
     * @param $ourCol
     * @param $tarCol
     * @return $this
     */
    public function makeRelation($type = 'one', $table, $ourCol, $tarCol)
    {
        MigrationManager::$migrationRelations[$type][$this->table] = [
            $table => [$ourCol, $tarCol]];

        return $this;
    }
    /**
     * get all args
     *
     * @param mixed $param
     * @return array
     */
    private function madeArray($param)
    {
        return func_num_args() === 1 ? [$param] : func_get_args();
    }

    /**
     * build blueprint command
     *
     * @param string $type
     * @param array $variables
     * @return Command
     */
    private function addCommand($type, $variables, $childPatternType = null)
    {
        $childPattern = $this->subPatterns[$childPatternType];

        if (!empty($variables)) {
            array_unshift($variables, $this->patterns[$type]);

            $command = call_user_func_array('sprintf', $variables);
        } else {
            $command = $this->patterns[$type];
        }

        static::$sqlCommands[] = $command = new Command($command, $childPattern, $childPatternType);

        return $command;
    }

    /**
     * @return string
     */
    public function prepareRow()
    {
        $query = '';

        if (is_array(static::$sqlCommands)) {
            foreach (static::$sqlCommands as $command) {
                if ($command instanceof Command) {
                    $query .= $command->prepareCommand() . ",";
                }
            }
        }

        static::$sqlCommands = [];

        return rtrim($query, ",");

    }
}
