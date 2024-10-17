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
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
    


}

