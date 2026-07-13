<?php

use Illuminate\Support\Facades\Event;
use Modules\Settings\Events\SettingUpdated;
use Modules\Settings\Models\Setting;
use Modules\Settings\Services\SettingsService;

beforeEach(function () {
    $this->service = app(SettingsService::class);
    $this->service->flush();
});

describe('get', function () {
    it('returns a type-cast value', function () {
        Setting::create(['key' => 'poll', 'value' => '30', 'type' => 'integer', 'group' => 'notification']);
        $this->service->flush();
        expect($this->service->get('poll'))->toBe(30);
    });

    it('casts boolean settings', function () {
        Setting::create(['key' => 'flag', 'value' => '1', 'type' => 'boolean', 'group' => 'general']);
        $this->service->flush();
        expect($this->service->get('flag'))->toBeTrue();
    });

    it('returns the default when the key is missing', function () {
        expect($this->service->get('missing', 'fallback'))->toBe('fallback');
    });
});

describe('set', function () {
    it('updates a value and fires SettingUpdated', function () {
        Setting::create(['key' => 'app_name', 'value' => 'Old', 'type' => 'string', 'group' => 'general']);
        $this->service->flush();
        Event::fake([SettingUpdated::class]);
        $this->service->set('app_name', 'New');
        expect($this->service->get('app_name'))->toBe('New');
        Event::assertDispatched(SettingUpdated::class);
    });

    it('round-trips an encrypted value', function () {
        Setting::create(['key' => 'secret', 'value' => '', 'type' => 'string', 'group' => 'general', 'is_encrypted' => true]);
        $this->service->flush();
        $this->service->set('secret', 'sensitive');
        expect(Setting::where('key', 'secret')->value('value'))->not->toBe('sensitive');
        expect($this->service->get('secret'))->toBe('sensitive');
    });
});

describe('group / publicValues', function () {
    it('returns only settings within a group', function () {
        Setting::create(['key' => 'a', 'value' => '1', 'type' => 'string', 'group' => 'alpha']);
        Setting::create(['key' => 'b', 'value' => '2', 'type' => 'string', 'group' => 'beta']);
        $this->service->flush();
        expect($this->service->group('alpha')->keys()->all())->toBe(['a']);
    });

    it('exposes only public settings as key => value', function () {
        Setting::create(['key' => 'pub', 'value' => 'x', 'type' => 'string', 'group' => 'general', 'is_public' => true]);
        Setting::create(['key' => 'priv', 'value' => 'y', 'type' => 'string', 'group' => 'general', 'is_public' => false]);
        $this->service->flush();
        $public = $this->service->publicValues();
        expect($public)->toHaveKey('pub');
        expect($public)->not->toHaveKey('priv');
    });
});
