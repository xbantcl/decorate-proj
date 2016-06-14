<?php namespace Decorate\Models;

class File extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'file';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'size' => 'int',
        'width' => 'int',
        'height' => 'int',
        'duration' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

}
