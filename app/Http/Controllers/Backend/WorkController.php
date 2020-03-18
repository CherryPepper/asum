<?php

namespace App\Http\Controllers\Backend;

use App\Models\Backend\Query;
use App\Models\MetersValue;
use App\Models\Meter;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class WorkController extends BaseController
{
    public function createMeters($count){
        $meters = [];
        $time = Carbon::now()->toDateTimeString();

        for($i = 1; $i <= $count; $i++){
            $meters[] = [
                'parent_id' => 0,
                'user_id' => $i,
                'nst_lvl' => 1,
                'type_id' => 1,
                'status_id' => 1,
                'value' => rand(3000, 10000),
                'ip_address' => "meter{$i}.asum",
                'login' => 'test',
                'password' => 'test',
                'serial' => time().$i,
                'rate_id' => 1,
                'operator_id' => 1,
                'address_id' => $i,
                'created_at' => $time
            ];
        }

        $chunks = collect($meters)->chunk(2000);
        $chunks->each(function($chunk){
            Meter::insert($chunk->toArray());
        });
    }

    public function createQueries($count){
        $queries = [];
        $time = Carbon::now()->toDateTimeString();

        for($i = 1; $i <= $count; $i++){
            $queries[] = [
                'meter_id' => 0,
                'action_id' => 1,
                'request' => '{"meter_ip":"localhost.welcome\/meters\/1","serial":"2"}',
                'response' => '{"queryid":"975","msgid":null,"param":"error","value":"1001"}',
                'created_at' => $time,
                'completed_at' => $time,
                'code_error' => '1001'
            ];
        }

        $chunks = collect($queries)->chunk(500);
        $chunks->each(function($chunk){
            Query::insert($chunk->toArray());
        });
    }

    public function createValuesTables($count, $filling = false){
        $model = new MetersValue();
        $last = 221;

        for ($i = $last; $i <= $last+$count; $i++){
            $model->setTable('meter_id_'.$i);
            $model->meter_id = $i;

            $this->createValueTable($model, $filling);
        }
    }

    private function createValueTable($model, $filling){
        if(!$model->schema->hasTable($model->table)){
            $model->schema->create($model->table, function($table){
                $table->increments('id');
                $table->dateTime('time_point');//->unique();
                $table->smallInteger('time_of_day')->nullable()->default(null);
                $table->integer('meter_id');
                $table->decimal('value', 9, 2)->nullable()->default(null);
                $table->decimal('difference', 7, 2)->nullable()->default(null);
                $table->decimal('accruals', 9, 2)->nullable()->default(null);
                $table->integer('query_id')->nullable()->default(null);
                $table->integer('error_code')->nullable()->default(null);
            });

            if($filling != false){
                $date = 1495544400;
                $meter_value = 0;
                $sql = '';
                $inc = 1;

                for ($i = 1; $i <= 175200; $i++, $inc++){
                    $difference = round($this->rand(),2);
                    $meter_value += $difference;
                    $date += 900;
                    $date_ymd = date('Y-m-d H:i:s', $date);
                    $accruals = round($difference*2,26, 2);

                    $sql .= "('{$date_ymd}', 0, {$model->meter_id}, {$meter_value}, {$difference}, {$accruals})";
                    $sql .= ($inc != 500) ? ',' : ';';

                    if($inc == 500){
                        $sql_tmp = 'insert into '.$model->table.' (time_point, time_of_day, meter_id, `value`, difference, accruals) values'.$sql;
                        $model->connect->insert($sql_tmp);

                        $sql = '';
                        $inc = 0;
                    }
                }
            }
        }
    }

    public function dropValuesTables(){
        $model = new MetersValue();
        $tables = $model->connect->select('SHOW TABLES');

        foreach ($tables as $table){
            foreach ($table as $key => $name){
                $id = explode('_', $name)[2];

                if($id > 220)
                    $this->dropValueTable($name);
            }
        }
    }

    private function dropValueTable($name){
        $model = new MetersValue();
        return $model->connect->select("DROP TABLE IF EXISTS {$name}");
    }

    private function rand($min = 0, $max = 5){return ($min + ($max - $min) * (mt_rand() / mt_getrandmax()));}
}
