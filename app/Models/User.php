<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function setPasswordAttribute($value){
        $this->attributes['password']=Hash::make($value);
    }
    // علاقة المستخدم بالمهام
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
   // علاقة many-to-many مع Role
   public function roles()
   {
       return $this->belongsToMany(Role::class,'role_user')->withTimestamps();
   }
   //to check if the user has permission
 public function hasPermission(String $permission){
    return $this->roles()->whereHas('permission',function($p)use($permission){
        $p->where('name',$permission);
    })->exists();

 }
 //revoke role from user
 public function revokeRole($id){
    $this->roles()->detach($id);
 }
 //grant role to user
public function grantRole(array $roles){
    $this->roles()->syncWithoutDetaching( $roles);
}
}
