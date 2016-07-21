<?php namespace Decorate\Models;

class Recommend extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'recommend';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

}
