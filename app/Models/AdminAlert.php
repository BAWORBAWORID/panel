<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int    $id
 * @property string $title
 * @property string $message
 * @property string $type
 * @property string $icon
 * @property string $position
 * @property string $bg_color
 * @property string $border_color
 * @property string $text_color
 * @property bool   $dismissable
 * @property bool   $active
 * @property int    $created_by
 */
class AdminAlert extends Model
{
    use HasFactory;

    protected $table = 'admin_alerts';

    protected $fillable = [
        'title',
        'message',
        'type',
        'icon',
        'position',
        'bg_color',
        'border_color',
        'text_color',
        'dismissable',
        'active',
        'created_by',
    ];

    protected $casts = [
        'dismissable' => 'boolean',
        'active'      => 'boolean',
    ];

    public const TYPES     = ['info', 'warning', 'danger', 'success', 'maintenance'];
    public const ICONS     = ['megaphone', 'warning', 'success', 'database', 'message', 'gear', 'rocket', 'reception'];
    public const POSITIONS = ['sticky', 'static'];

    public static array $validationRules = [
        'title'        => 'required|string|max:120',
        'message'      => 'required|string|max:1000',
        'type'         => 'required|in:info,warning,danger,success,maintenance',
        'icon'         => 'required|in:megaphone,warning,success,database,message,gear,rocket,reception',
        'position'     => 'required|in:sticky,static',
        'bg_color'     => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'border_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'text_color'   => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'dismissable'  => 'required|boolean',
        'active'       => 'sometimes|boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public static function getActive(): ?self
    {
        return static::with('creator')->active()->latest()->first();
    }
}
