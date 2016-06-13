<?php namespace Decorate\Models;

class Diary extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'diary';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'decorate_progress' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

}
