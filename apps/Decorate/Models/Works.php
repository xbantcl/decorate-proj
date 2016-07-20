<?php namespace Decorate\Models;

class Works extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'works';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'intr' => 'string',
        'address' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];
}
