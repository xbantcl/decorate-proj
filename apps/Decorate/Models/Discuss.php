<?php namespace Decorate\Models;

class Discuss extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'discuss';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'label_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'label_id' => 'int',
        'content' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
