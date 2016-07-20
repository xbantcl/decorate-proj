<?php namespace Decorate\Models;

class WorksFile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'works_file';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'works_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'works_id' => 'int',
        'file_id' => 'int',
        'file_url' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];
}
