<?php

namespace Modules\Settings\Services;

use App\Core\Services\BaseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Modules\Settings\Events\SettingUpdated;
use Modules\Settings\Models\Setting;

class SettingsService extends BaseService
{
    private const CACHE_KEY = 'settings.all';

    /**
     * Get a single setting's cast value, or the default when missing.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->all()->get($key);

        return $setting?->casted_value ?? $default;
    }

    /**
     * Set a setting value, fire SettingUpdated, and bust the cache.
     */
    public function set(string $key, mixed $value): Setting
    {
        $setting = Setting::where('key', $key)->firstOrFail();

        $oldValue = $setting->casted_value;

        $stored = is_array($value) ? json_encode($value) : (string) $value;

        if ($setting->is_encrypted && $stored !== '') {
            $stored = Crypt::encryptString($stored);
        }

        $setting->update(['value' => $stored]);
        $this->flush();

        event(new SettingUpdated($setting->fresh(), $oldValue, $value));

        return $setting;
    }

    /**
     * All settings for a given group, keyed by setting key.
     *
     * @return Collection<string, Setting>
     */
    public function group(string $group): Collection
    {
        return $this->all()->filter(fn (Setting $s) => $s->group === $group);
    }

    /**
     * All settings keyed by setting key (cached forever until flushed).
     *
     * @return Collection<string, Setting>
     */
    public function all(): Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => Setting::all()->keyBy('key'));
    }

    /**
     * Public settings as a key => cast-value map for sharing with the frontend.
     *
     * @return array<string, mixed>
     */
    public function publicValues(): array
    {
        return $this->all()
            ->filter(fn (Setting $s) => $s->is_public)
            ->mapWithKeys(fn (Setting $s) => [$s->key => $s->casted_value])
            ->all();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
