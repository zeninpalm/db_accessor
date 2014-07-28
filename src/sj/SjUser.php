<?php

require_once __DIR__ . '/../common/User.php';

class SjUser extends User {
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
                $this->latest_login_time = time();
                $this->login_times = 1;
            }
            return true;
        }
        return false;
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,mobile, password, role', 'required'),
            array('role, create_time, latest_login_time, login_times , header_id, address_id', 'numerical', 'integerOnly' => true),
            array('mobile', 'length', 'max' => 20),
            array('name', 'length', 'max' => 50),
            array('email, password', 'length', 'max' => 100),
            array('header', 'length', 'max' => 500),
            array('email', 'isRepeatEmail'),
            array('email', 'email'),
            array('name', 'isRepeatName'),
            array('mobile', 'isRepeatMobile'),
            array('header', 'file', 'types' => 'jpg,png', 'maxSize' => 1024 * 30, 'allowEmpty' => true, 'tooLarge' => '图片大小不能超过30KB'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, mobile, name, email, password, role, header, create_time, latest_login_time, login_times', 'safe', 'on' => 'search'),
        );
    }

    public function relations() {
        return array(
            'companies' => array(self::HAS_MANY, 'Company', 'user_id', 'scopes' => array('resetScope')),
            'routers' => array(self::HAS_MANY, 'Router', 'user_id'),
            'address' => array(self::BELONGS_TO, 'Address', 'address_id'),
            'charges' => array(self::HAS_MANY, 'Charge', 'user_id'),
            'user_betas' => array(self::HAS_MANY, 'UserBeta', 'user_id'),

        );
    }

    public function attributeLabels()
    {
    	$labels = parent::attributeLabels();
        $additionals =  array(
            'province'=>'省份',
            'city'=>'城市',
            'latest_login_time' => '上次登录时间',
            'login_times' => '登录次数',
        );
        return array_merge($labels, $additionals);
    }

    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('role', $this->role);
        $criteria->compare('header', $this->header, true);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('latest_login_time', $this->latest_login_time);
        $criteria->compare('login_times', $this->login_times);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public function getRechargeStatistic()
    {
        $tmp1 = 0;
        $tmp2 = 0;
        $tmp3 = 0;
        $tmp4 = 0;
        foreach ($this->charges as $charge) {
            if ($charge->type == 1) {
                $tmp1++;
                $tmp2 += $charge->cost;
            }
            if ($charge->type == 2) {
                $tmp3++;
                $tmp4 += $charge->cost;
            }
        }
        $this->_statistics[0] = $tmp1;
        $this->_statistics[1] = $tmp2;
        $this->_statistics[2] = $tmp3;
        $this->_statistics[3] = $tmp4;
    }

    public function getStatistics()
    {
        if (count($this->_statistics) != 4) {
            $this->getRechargeStatistic();
        }
        return $this->_statistics;
    }

    public function judgeFunction($param)
    {
        foreach ($this->user_betas as $userBeta) {
            if ($userBeta->beta->name == $param) {
                return true;
            }
        }
        return false;
    }
}