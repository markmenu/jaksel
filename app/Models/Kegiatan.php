<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Metadata;

class Kegiatan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * Mendefinisikan relasi ke Metadata.
     */
    public function metadata()
    {
        return $this->belongsTo(Metadata::class);
    }

    /**
     * Mendefinisikan relasi ke Users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'kegiatan_user')
                    ->withPivot('role_in_kegiatan')
                    ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Kegiatan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Kegiatan::class, 'parent_id');
    }
}
