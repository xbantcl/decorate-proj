<?php namespace Decorate\Models;

class DiscussComment extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'discuss_comment';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'uid' => 'int',
        'discuss_id' => 'int',
        'parent_id' => 'int',
        'target_uid' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'uid' => 'int',
        'discuss_id' => 'int',
        'parent_id' => 'int',
        'target_uid' => 'int',
        'content' => 'string',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
