<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowChecklist extends Model
{
    protected $table = 'workflow_checklists';

    protected $fillable = [
        'module',
        'reference_type',
        'reference_id',
        'reference_code',
        'step_code',
        'step_name',
        'step_order',
        'is_required',
        'is_completed',
        'completed_at',
        'completed_by',
        'notes',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Dapatkan record sumber (polymorphic manual).
     */
    public function reference()
    {
        $class = $this->reference_type;
        if (class_exists($class)) {
            return $class::find($this->reference_id);
        }
        return null;
    }

    /**
     * Scope: hanya yang belum selesai
     */
    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope: hanya yang sudah selesai
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope: filter per modul
     */
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope: filter per referensi dokumen
     */
    public function scopeForReference($query, string $referenceType, int $referenceId)
    {
        return $query->where('reference_type', $referenceType)
                     ->where('reference_id', $referenceId);
    }
}
