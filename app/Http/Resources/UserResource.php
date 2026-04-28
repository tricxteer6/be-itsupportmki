<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id_karyawan' => $this->id_karyawan,
            'nama_karyawan' => $this->nama_karyawan,
            'divisi' => $this->divisi,
            'posisi_jabatan' => $this->posisi_jabatan,
            'role' => $this->role,
            'email' => $this->email,
            'no_telp' => $this->no_telp,
            'created_at' => $this->created_at,
        ];
    }
}
