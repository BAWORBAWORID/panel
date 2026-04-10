<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Pterodactyl\Models\UserMusicPreference
 *
 * @property int $id
 * @property int $user_id
 * @property int $volume   0–100
 * @property bool $shuffle
 * @property bool $repeat
 * @property int|null $current_track_id
 */
class UserMusicPreference extends Model
{
    protected $table = 'user_music_preferences';

    protected $fillable = [
        'user_id',
        'volume',
        'shuffle',
        'repeat',
        'current_track_id',
    ];

    protected $casts = [
        'volume'  => 'integer',
        'shuffle' => 'boolean',
        'repeat'  => 'boolean',
    ];

    protected $attributes = [
        'volume'  => 70,
        'shuffle' => false,
        'repeat'  => false,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Pterodactyl\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
