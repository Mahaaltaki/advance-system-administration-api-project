<?php
namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            
            'name' => $this->name,
            'description' => $this->description,
            
        ];
    }
}
?>