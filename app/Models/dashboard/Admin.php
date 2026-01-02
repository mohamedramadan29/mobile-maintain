<?php

namespace App\Models\dashboard;

use App\Models\dashboard\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'type',
        'status',
        'remember_token'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function Role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasAccess($config_permission)
    {
        $role = $this->Role;
       // dd($role);
        if (!$role) {
            return false;
        }
        $permissions = json_decode($role->permission);
        foreach ($permissions as $permission) {
            if ($permission == $config_permission ?? false) {
                return true;
            }
        }
    }


}
