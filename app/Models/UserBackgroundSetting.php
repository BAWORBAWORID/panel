<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $mode             none|pattern|image
 * @property string|null $pattern
 * @property int    $pattern_size
 * @property string|null $image_url
 * @property string $filter           none|blur|dim
 * @property int    $blur_amount
 * @property int    $transparency
 * @property string $pattern_color1
 * @property string $pattern_color2
 */
class UserBackgroundSetting extends Model
{
    protected $table = 'user_background_settings';

    protected $fillable = [
        'user_id',
        'mode',
        'pattern',
        'pattern_size',
        'image_url',
        'filter',
        'blur_amount',
        'transparency',
        'pattern_color1',
        'pattern_color2',
    ];

    protected $casts = [
        'pattern_size' => 'integer',
        'blur_amount'  => 'integer',
        'transparency' => 'integer',
    ];

    public const MODES    = ['none', 'pattern', 'image'];
    public const FILTERS  = ['none', 'blur', 'dim'];
    public const PATTERNS = [
        'tiles', 'cubes', 'rotated-squares', 'l-shape', 'zig-zag',
        'wavy-checkerboard', 'chevrons', 'houndstooth', 'quarter-circles',
        'diagonal-rectangles', 'alternating-arc', 'rotated-rectangles',
        'concentric-arrows', 'outline-triangles', 'moon', 'polka',
    ];

    public static array $validationRules = [
        'mode'           => 'required|in:none,pattern,image',
        'pattern'        => 'nullable|in:tiles,cubes,rotated-squares,l-shape,zig-zag,wavy-checkerboard,chevrons,houndstooth,quarter-circles,diagonal-rectangles,alternating-arc,rotated-rectangles,concentric-arrows,outline-triangles,moon,polka',
        'pattern_size'   => 'integer|min:50|max:500',
        'image_url'      => 'nullable|url|max:512',
        'filter'         => 'in:none,blur,dim',
        'blur_amount'    => 'integer|min:0|max:20',
        'transparency'   => 'integer|min:0|max:3',
        'pattern_color1' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'pattern_color2' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get defaults as array (for new users).
     */
    public static function defaults(): array
    {
        return [
            'mode'           => 'none',
            'pattern'        => null,
            'pattern_size'   => 100,
            'image_url'      => null,
            'filter'         => 'none',
            'blur_amount'    => 4,
            'transparency'   => 0,
            'pattern_color1' => '#1e293b',
            'pattern_color2' => '#334155',
        ];
    }
}
