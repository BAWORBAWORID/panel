<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pterodactyl\Models\UserMusicTrack
 *
 * @property int $id
 * @property int $user_id
 * @property string $source  'global' | 'youtube' | 'upload'
 * @property string $title
 * @property string|null $artist
 * @property string|null $video_id
 * @property string|null $file_path
 * @property string|null $download_url
 * @property int|null $duration_seconds
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class UserMusicTrack extends Model
{
    protected $table = 'user_music_tracks';

    protected $fillable = [
        'user_id',
        'source',
        'title',
        'artist',
        'video_id',
        'file_path',
        'download_url',
        'duration_seconds',
        'sort_order',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'sort_order'       => 'integer',
    ];

    public static array $validationRules = [
        'source'    => 'required|in:global,youtube,upload',
        'title'     => 'required|string|max:255',
        'artist'    => 'nullable|string|max:255',
        'video_id'  => 'nullable|string|max:50',
        'file_path' => 'nullable|string|max:500',
    ];

    /**
     * Resolve the public URL for this track's audio file.
     * Returns null if the track has no resolvable URL.
     */
    public function getPublicUrlAttribute(): ?string
    {
        return match ($this->source) {
            'global'   => '/assets/mp3/spotify2026.mp3',
            'upload'   => $this->file_path
                            ? '/storage/music/' . $this->user_id . '/' . basename($this->file_path)
                            : null,
            'youtube'  => $this->download_url,
            default    => null,
        };
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Pterodactyl\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
