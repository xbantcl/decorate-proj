<?php namespace Decorate\Models;

class DiscussFile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'discuss_file';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'discuss_id' => 'int',
        'file_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'discuss_id' => 'int',
        'file_id' => 'int',
        'file_url' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
