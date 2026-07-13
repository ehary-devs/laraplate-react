<?php

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

it('guards the id field from mass assignment', function () {
    $model = new class extends BaseModel
    {
        protected $table = 'users';
    };

    expect($model->getGuarded())->toContain('id');
});

it('extends the Eloquent base model', function () {
    $model = new class extends BaseModel
    {
        protected $table = 'users';
    };

    expect($model)->toBeInstanceOf(Model::class);
});
