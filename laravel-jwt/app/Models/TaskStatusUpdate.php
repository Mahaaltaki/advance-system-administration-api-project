<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'status',
    ];
    public function task(){
        return $this->belongsTo(Task::class);
    }
}
