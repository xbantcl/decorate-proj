<?php namespace Decorate\Models;

class DiscussCommentFile extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'discuss_comment_file';

    public $guarded = ['id'];

    protected $casts = [
        'id' => 'int',
        'discuss_comment_id' => 'int',
        'file_id' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int'
    ];

    public static $rules = [
        'discuss_comment_id' => 'int',
        'file_id' => 'int',
        'file_url' => 'int',
        'insert_time' => 'int',
        'modify_time' => 'int',
    ];
}
