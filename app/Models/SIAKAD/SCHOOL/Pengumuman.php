<?php

namespace App\Models\SIAKAD\SCHOOL;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'judul',
        'isi_pengumuman',
        'target_role',
        'status',
        'scheduled_for',
        'sent_at',
        'id_admin',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
    ];

    protected static ?string $cachedPrimaryKey = null;
    protected static ?bool $cachedNewAttachmentCols = null;

    public static function primaryKeyColumn(): ?string
    {
        if (static::$cachedPrimaryKey !== null) {
            return static::$cachedPrimaryKey;
        }
        if (Schema::hasColumn('pengumuman', 'id')) {
            static::$cachedPrimaryKey = 'id';
        } elseif (Schema::hasColumn('pengumuman', 'id_pengumuman')) {
            static::$cachedPrimaryKey = 'id_pengumuman';
        } else {
            static::$cachedPrimaryKey = null;
        }
        return static::$cachedPrimaryKey;
    }

    public static function usesNewAttachmentColumns(): bool
    {
        if (static::$cachedNewAttachmentCols !== null) {
            return static::$cachedNewAttachmentCols;
        }
        static::$cachedNewAttachmentCols = Schema::hasColumn('pengumuman_attachments', 'file_name');
        return static::$cachedNewAttachmentCols;
    }

    // Relationships
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'users_id');
    }

    public function attachments()
    {
        $localKey = static::primaryKeyColumn() ?: $this->getKeyName();
        return $this->hasMany(PengumumanAttachment::class, 'pengumuman_id', $localKey);
    }

    public function getKeyName()
    {
        return static::primaryKeyColumn() ?: parent::getKeyName();
    }

    // Scopes
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (!$status || $status === 'any' || $status === 'semua') {
            return $query;
        }
        return $query->where('status', $status);
    }

    public function scopeFilterTargetRole(Builder $query, ?string $role): Builder
    {
        if (!$role || $role === 'semua' || $role === 'any') {
            return $query;
        }
        return $query->where('target_role', $role);
    }

    public function scopeFilterDateRange(Builder $query, ?string $range): Builder
    {
        if (!$range || $range === 'any') {
            return $query;
        }
        $map = [
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            '6months' => now()->subMonths(6),
            'year' => now()->subYear(),
        ];
        if (!isset($map[$range])) {
            return $query;
        }
        return $query->where(function ($q) use ($map, $range) {
            $q->where('sent_at', '>=', $map[$range])
                ->orWhere(function ($sub) use ($map, $range) {
                    $sub->whereNull('sent_at')
                        ->where('created_at', '>=', $map[$range]);
                });
        });
    }

    // Helpers
    public function statusLabel(): string
    {
        return [
            'sent' => 'Diumumkan',
            'scheduled' => 'Dijadwalkan',
            'draft' => 'Draf',
            'published' => 'Diumumkan',
        ][$this->status] ?? $this->status;
    }

    public function statusClass(): string
    {
        return [
            'sent' => 'ann-status--sent',
            'scheduled' => 'ann-status--scheduled',
            'draft' => 'ann-status--draft',
            'published' => 'ann-status--sent',
        ][$this->status] ?? '';
    }

    public function statusIcon(): string
    {
        return [
            'sent' => 'public',
            'scheduled' => 'schedule',
            'draft' => 'description',
            'published' => 'public',
        ][$this->status] ?? 'public';
    }

    public function targetLabel(): string
    {
        return [
            'guru' => 'Guru',
            'ortu' => 'Ortu',
            'all' => 'Semua',
            'semua' => 'Semua',
        ][$this->target_role] ?? $this->target_role;
    }

    public function waktuSingkat(): string
    {
        $date = $this->sent_at ?? $this->scheduled_for ?? $this->created_at;
        if (!$date) {
            return '-';
        }

        $date = $date->locale('id');
        return $date->isSameYear(now())
            ? $date->translatedFormat('j M')
            : $date->format('d/m/y');
    }

    public function metaDateLong(): string
    {
        $date = $this->sent_at ?? $this->scheduled_for ?? $this->created_at;
        return $date ? $date->locale('id')->translatedFormat('j M Y, H.i') : '-';
    }
}
