<?php

use App\Core\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Model;

it('returns uuid as the route key name', function () {
    $model = new class extends Model
    {
        use HasPublicId;
    };

    expect($model->getRouteKeyName())->toBe('uuid');
});
