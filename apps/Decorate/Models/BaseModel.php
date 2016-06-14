<?php namespace Decorate\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $autoTime = true;

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (BaseModel $model) {
            if ($model->autoTime) {
                $model->insert_time = time();
            }
        });

        static::saving(function (BaseModel$model) {
            if ($model->autoTime) {
                $model->modify_time = time();
            }
        });
    }

}