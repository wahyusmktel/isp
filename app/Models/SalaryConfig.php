<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryConfig extends Model
{
    protected $fillable = ['jabatan', 'base_salary', 'allowance', 'notes'];

    public function toJsonData(): array
    {
        return [
            'id'           => $this->id,
            'jabatan'      => $this->jabatan,
            'base_salary'  => $this->base_salary,
            'allowance'    => $this->allowance,
            'notes'        => $this->notes ?? '',
        ];
    }
}
