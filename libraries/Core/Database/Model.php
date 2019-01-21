<?php 
namespace LegoAsync\Database; 
use Illuminate\Database\Eloquent\Model as EloquentModel;
use LegoAsync\Kernel\Application;
use Illuminate\Database\Capsule\Manager as DbManager;

class Model extends EloquentModel
{
    protected $db;
    public function __construct($attributes = [], $connection = 'default')
    {
        $this->db = Application::getInstance()->getContainer()['db'];
        if(!($this->db instanceof DbManager))
        {
            throw new \RuntimeException("Could not detect connection");
        }
        $this->connection = $connection;
        parent::__construct($attributes);
    }
    /**
     * Get DB Connection
     * @return \Illuminate\Database\Capsule\Manager
     */
    public function getDb()
    {
        return $this->db;
    }
}