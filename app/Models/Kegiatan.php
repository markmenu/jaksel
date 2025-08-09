<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Metadata;
use App\Models\User;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'file_pendukung',
        'status',
        'metadata_id',
        'progress',
        'pending_progress',
        'parent_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function metadata()
    {
        return $this->belongsTo(Metadata::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kegiatan_user')
            ->withPivot('role_in_kegiatan')
            ->withTimestamps();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'parent_id');
    }

    /**
     * Mendefinisikan relasi rekursif untuk memuat seluruh hierarki anak
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }
}
