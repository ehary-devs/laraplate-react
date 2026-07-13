<?php

namespace Modules\Settings\Models;

use App\Core\Models\BaseModel;
use Illuminate\Support\Facades\Crypt;

/**
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property bool $is_public
 * @property bool $is_encrypted
 * @property array<string, string>|null $options
 */
class Setting extends BaseModel
{
    protected $fillable = ['group', 'key', 'label', 'description', 'value', 'type', 'options', 'is_public', 'is_encrypted'];

    protected $casts = [
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'options' => 'array',
    ];

    /**
     * The decoded, type-cast value (decrypting first when stored encrypted).
     */
    public function getCastedValueAttribute(): mixed
    {
        $raw = $this->value;

        if ($this->is_encrypted && $raw !== null && $raw !== '') {
            $raw = Crypt::decryptString($raw);
        }

        return match ($this->type) {
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $raw,
            'json' => json_decode((string) $raw, true),
            default => $raw,
        };
    }
}
