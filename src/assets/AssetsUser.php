<?php

require_once __DIR__ . '/../common/User.php';

class AssetsUser extends User {
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('email, password', 'required'),
			array('mobile', 'length', 'max'=>20),
			array('name', 'length', 'max'=>50),
			array('email', 'length', 'max'=>100),
			array('header', 'length', 'max'=>500),
                        array('email', 'isRepeatEmail'),
                        array('name', 'isRepeatName'),
                        array('mobile', 'isRepeatMobile'),
			array('create_time', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, mobile, name, email, header, create_time', 'safe', 'on'=>'search'),
		);
	}

    public function isRepeatMobile(){
        if($this->mobile!=null&& strlen($this->mobile)>0){
            if(!preg_match(UserIdentity::$PHONE_MATCH, $this->mobile)){
                $this->addError('mobile', '请输入正确的手机号');
            }else{
                $this->isRepeat('mobile', $this->mobile, '手机号');
            }
        }
    }

	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('header',$this->header,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}