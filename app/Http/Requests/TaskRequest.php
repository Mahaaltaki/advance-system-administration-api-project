<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            
        'title'=>'required|string|max:50', 
        'description'=>'required|string|max:1000',
        'type'=>'required|in:bug,feature,improvement',
        'status'=>'required|in:open,inProgress,completed,blocked',
        'priority'=>'required|in:low,medium,high',
        'due_date'=>'required|date',
        'assigned_to'=>'required|integer',
        ];
    }
}
