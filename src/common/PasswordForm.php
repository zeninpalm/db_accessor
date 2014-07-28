<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class PasswordForm extends CFormModel {

    public $oldpass;
    public $newpass;
    public $repass;

    public function rules() {
        return array(
            array('oldpass, newpass, repass', 'required'),
            array('oldpass', 'authenticate'),
            array('newpass','length', 'min' => 6, 'max'=>20, 'tooShort'=>"{attribute}太短了.",'tooLong'=>"{attribute}太长了."),
            array('repass', 'compare', 'compareAttribute'=>'newpass'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'oldpass' => '原始密码',
            'newpass' => '新密码',
            'repass' => '重复新密码',
        );
    }

    public function authenticate() {
        if (!$this->hasErrors()) {
            $identity = new UserIdentity(Yii::app()->user->getName(), $this->oldpass);
            if (!$identity->authenticate()){
                $this->addError('oldpass', '原始密码错误.');
            }
        }
    }

}
