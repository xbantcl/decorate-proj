<?php namespace Passport\Modules;
/**
 * UserModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Passport\Models\User;
use Passport\Enum\UserType;

class UserModule extends BaseModule
{
    /**
     * 添加用户.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data) {
        $user = User::create($data);
        if (UserType::ORD_USER == $data['user_type']) {
            
        }
    }

}
 