<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable=[
        'title', 
        'description',
         'type',
         'status',
         'priority',
         'due_date',
         'assigned_to',
    ];
    public function comments(){
        return $this->morphTo(Comment::class,'commentable');
    }

    public function attachments()
{
    return $this->belongsToMany(Attachment::class);
}
public function taskStatusUpdate(){
    return $this->hasMany(TaskStatusUpdate::class);
}
    
// علاقة المهمة بالمستخدم
public function user()
{
    return $this->belongsTo(User::class, 'assigned_to');
}
// المهام التي تعتمد على هذه المهمة
public function dependents()
{
    return $this->hasMany(TaskDependency::class, 'task_id');
}
// المهام التي تعتمد عليها هذه المهمة
public function dependencies()
{
    return $this->hasMany(TaskDependency::class, 'dependent_task_id');
}
}
