<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->booking_code,
            'status'      => $this->status,
            'package'     => $this->slot?->package?->name,
            'date'        => $this->slot?->date,
            'items_count' => $this->items_count ?? $this->items()->count(),
            'total'       => $this->total_amount,
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
