<?php namespace Decorate\Models;

class DecorateLabel extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'decorate_label';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
    ];

}
