<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->date)->translatedFormat('d M. Y'),
            'time' => $this->time,
            'service' => is_null($this->service) ? 'Скажу по телефону' : $this->service,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'client_name' => $this->client_name,
        ];
    }
}
