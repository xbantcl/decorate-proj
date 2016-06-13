<?php namespace Decorate\Modules;
/**
 * DiaryModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\Diary;

class DiaryModule extends BaseModule
{
    /**
     * 添加装修日志.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data) {
        return Diary::create($data);
    }
}
 