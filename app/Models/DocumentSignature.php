<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignature extends Model
{
    protected $table = 'document_signatures';

    protected $fillable = [
        'jenis_dokumen',
        'posisi_kode',
        'label_judul',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'organisasi',
        'catatan',
        'director_id',
        'show_signature_line',
        'urutan',
    ];

    protected $casts = [
        'show_signature_line' => 'boolean',
        'urutan' => 'integer',
    ];

    public function director()
    {
        return $this->belongsTo(CompanyDirector::class, 'director_id');
    }

    /**
     * Scope / Helper to get signatures for a specific document type
     */
    public static function forDoc(string $jenisDokumen)
    {
        return static::where('jenis_dokumen', $jenisDokumen)
            ->orderBy('urutan')
            ->get();
    }
}
