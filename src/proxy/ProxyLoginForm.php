<?php
/**
 * Created by PhpStorm.
 * User: wei
 * Date: 14-7-28
 * Time: 下午2:37
 */

require_once __DIR__.'../common/LoginForm.php';

class ProxyLoginForm extends LoginForm{

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $addtion = array(
            'username'=>'账号',
            'password'=>'密码',
        );

        return array_merge($labels,$addtion);
    }
} 