<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function __construct($resource, protected bool $hideEmail = false)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'phone_number' => $this->phone_number,
            'pin' => $this->pin,
            'region' => $this->region,
            'uvd' => $this->uvd,
            'address' => $this->address,
            'icon' => $this->icon,
        ];
    }
}
