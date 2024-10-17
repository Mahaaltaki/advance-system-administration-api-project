<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;
        protected $fillable = ['file'];

        // تعريف العلاقة polymorphic
        public function attachable()
        {
            return $this->morphTo();
        }
    

}
