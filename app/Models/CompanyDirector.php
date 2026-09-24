<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDirector extends Model
{
    protected $table = 'company_directors';

    protected $fillable = [
        'nama',
        'jabatan',
        'nik',
        'npwp',
        'email',
        'telepon',
        'foto',
        'tanda_tangan',
        'keterangan',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function signatures()
    {
        return $this->hasMany(DocumentSignature::class, 'director_id');
    }
}
