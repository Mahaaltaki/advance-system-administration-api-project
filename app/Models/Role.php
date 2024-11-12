<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'description',
    ];
     // change the name of primary key feild
     protected $primaryKey = 'id';
    protected $hidden = [
        'id',
    ];
    // the feilds which we want show it in response
    protected $visible = [
        'name', 'description',
    ];
     // علاقة many-to-many مع User
     public function users()
     {
         return $this->belongsToMany(User::class)->withTimestamps();
     }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
    
//assign many permission to role by permissions id
//@param array $idPermissions
//@return void
public function grantPermissions(array $idPermissions){
    $this->permissions()->syncWithoutDetaching($idPermissions);
}
//remove permission from role by id
//@param $idPermission
//@return void
public function revokePermission($idPermission){
    $this->permissions()->detach($idPermission);
}
}

