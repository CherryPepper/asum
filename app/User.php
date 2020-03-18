<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'login',
        'email',
        'password',
        'role_id',
        'passport',
        'passport_mvd',
        'contract',
        'remember_token',
        'display'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role(){
        return $this->belongsTo('App\Models\Role');
    }

    public function meter(){
        return $this->hasOne('App\Models\Meter');
    }

    public function access_regions(){
        return $this->hasMany('App\Models\UserRegionsAccess');
    }

    public function scopeDeletedForAdmin($query){
        if(Auth::user()->role->slug != 'administrator')
            return $query->where('users.display', 1);
    }

    public function scopeJoinAddress($query){
        return $query->join('meters as m', 'm.user_id', '=', 'users.id')
            ->join('addresses as a', 'a.id', '=', 'm.address_id')
            ->select('users.*');
    }

    public function scopeCheckAccess($query){
        if(Auth::user()->role->slug !== 'administrator'){
            $allowed_str = '';
            $allowed = Auth::user()->access_regions()->with('address')->get();

            foreach ($allowed as $alw)
                $allowed_str .= $alw->address->path_childs.',';
            $allowed_str = rtrim($allowed_str, ',');

            return $query->whereIn("address_id", explode(',', $allowed_str));
        }
    }

    public function scopeTaskReport($query, $params){
        $now = Carbon::now()->format('Y-m-d');
        $date_from = !empty($params->date_from) ? Carbon::parse($params->date_from)->format('Y-m-d') : '1975-01-01';
        $date_to = !empty($params->date_to) ? Carbon::parse($params->date_to)->format('Y-m-d') : $now;

        return $query->addSelect('users.*')
            ->addSelect(\DB::raw("(SELECT COUNT(id) FROM tasks WHERE employer_id=users.id 
                AND created_at >= '{$date_from}' AND created_at <= '{$date_to}' AND deleted_at IS NULL) as all_tasks"))
            ->addSelect(\DB::raw("(SELECT COUNT(id) FROM tasks WHERE employer_id=users.id 
                AND (date_end < '{$now}' OR date_end < date_complete)
                AND created_at >= '{$date_from}' AND created_at <= '{$date_to}' AND deleted_at IS NULL) as overdue"))
            ->addSelect(\DB::raw("(SELECT COUNT(id) FROM tasks WHERE employer_id=users.id 
                AND date_end > date_complete
                AND created_at >= '{$date_from}' AND created_at <= '{$date_to}' AND deleted_at IS NULL) as completed"))
            ->addSelect(\DB::raw("(SELECT COUNT(id) FROM tasks WHERE employer_id=users.id 
                AND date_complete IS NULL AND date_end >= '{$now}'
                AND created_at >= '{$date_from}' AND created_at <= '{$date_to}' AND deleted_at IS NULL) as in_progress"));
    }




    public static function userRole(){
        return self::where('id', Auth::user()->id)->with(['role'])->firstOrFail();
    }

    public function hasRole($roles){
        $user_role = self::userRole()->role->slug;
        $roles = explode('.', $roles);

        if(in_array($user_role, $roles))
            return true;
        else
            return false;
    }
}
