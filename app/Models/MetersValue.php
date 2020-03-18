<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Schema;
use DB;

class MetersValue extends Model
{
    public $schema = [];

    public $connect = [];

    public $table;

    public $connection = 'db_meters_value';

    public $timestamps = false;

    protected $fillable = [
        'id', 'time_point', 'time_of_day', 'meter_id', 'value',
        'difference', 'accruals', 'query_id', 'error_code'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection($this->connection);

        $this->schema = Schema::connection($this->connection);
        $this->connect = DB::connection($this->connection);
    }

    public function createTable(){
        if(!$this->schema->hasTable($this->table)) {
            $this->schema->create($this->table, function ($table) {
                $table->increments('id');
                $table->dateTime('time_point')->unique();
                $table->smallInteger('time_of_day')->nullable()->default(null);
                $table->integer('meter_id');
                $table->decimal('value', 9, 2)->nullable()->default(null);
                $table->decimal('difference', 7, 2)->nullable()->default(null);
                $table->decimal('accruals', 9, 2)->nullable()->default(null);
                $table->integer('query_id')->nullable()->default(null);
                $table->integer('error_code')->nullable()->default(null);
            });
        }
    }

    public function dropTable(){
        return $this->connect->select("DROP TABLE IF EXISTS {$this->table}");
    }
}
