<?php namespace Decorate\Models;

class Collection extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'collection';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'type' => 'int',
        'dataId' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'type' => 'int',
        'data_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
