<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nama_metadata
 * @property string|null $keterangan
 * @property string|null $file_metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Kegiatan|null $kegiatan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereFileMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereNamaMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Metadata whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Metadata extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_metadata',
        'keterangan',
        'file_metadata',
    ];

    /**
     * Mendefinisikan relasi one-to-one ke Kegiatan.
     */
    public function kegiatan()
    {
        return $this->hasOne(Kegiatan::class);
    }
}
