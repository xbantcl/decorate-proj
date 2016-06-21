<?php namespace Decorate\Models;

class DiaryFile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'diary_file';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'diary_id' => 'int',
        'file_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'diary_id' => 'int',
        'file_id' => 'int',
        'file_url' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
