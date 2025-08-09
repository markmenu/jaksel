<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'source_task_id',
        'target_task_id',
    ];

    /**
     * Relasi ke task sumber (Kegiatan).
     */
    public function source()
    {
        return $this->belongsTo(Kegiatan::class, 'source_task_id');
    }

    /**
     * Relasi ke task target (Kegiatan).
     */
    public function target()
    {
        return $this->belongsTo(Kegiatan::class, 'target_task_id');
    }
}
