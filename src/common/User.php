<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $mobile
 * @property string $name
 * @property string $email
 * @property string $password
 * @property integer $role
 * @property string $header
 * @property string $header_id
 * @property string $address_id
 * @property integer $create_time
 * @property integer $latest_login_time
 * @property integer $login_times
 *
 * The followings are the available model relations:
 * @property Company[] $companies
 * @property Address $address
 * @property Rharge[] $charges
 * @property UserBeta[] $user_betas
 */
class User extends CActiveRecord
{

    public static $USER_ROLES = array('0' => '超级管理员', '1' => '管理员', '2' => '商家');

    public $province;
    public $city;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user';
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
            }
            return true;
        }
        return false;
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name,mobile, password, role', 'required'),
            array('role, create_time, header_id, address_id', 'numerical', 'integerOnly' => true),
            array('mobile', 'length', 'max' => 20),
            array('name', 'length', 'max' => 50),
            array('email, password', 'length', 'max' => 100),
            array('header', 'length', 'max' => 500),
            array('email', 'isRepeatEmail'),
            array('name', 'isRepeatName'),
            array('mobile', 'isRepeatMobile'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, mobile, name, email, password, role, header, create_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'companies' => array(self::HAS_MANY, 'Company', 'user_id', 'scopes' => array('resetScope')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'mobile' => '手机',
            'address_id' => '区域',
            'name' => '用户名',
            'email' => '邮箱',
            'password' => '密码',
            'role' => '角色',
            'header' => '头像',
            'header_id' => '头像资源ID',
            'create_time' => '创建时间',
        );
    }


    public function isRepeatEmail()
    {
        if ($this->email != null && strlen(trim($this->email)) > 0) {
            $this->isRepeat('email', $this->email, '注册邮箱');
        }
    }

    public function isRepeatMobile()
    {
        if ($this->mobile != null && strlen($this->mobile) > 0) {
            if (!preg_match(PhoneForm::$PHONE_MATCH, $this->mobile)) {
                $this->addError('mobile', '请输入正确的手机号');
            } else {
                $this->isRepeat('mobile', $this->mobile, '手机号');
            }
        }
    }

    public function isRepeatName()
    {
        if ($this->name != null && strlen($this->name) > 0) {
            $this->isRepeat('name', $this->name, '用户名');
        }
    }

    protected function isRepeat($attr, $value, $msg)
    {
        if (count($this->getErrors($attr)) == 0) {
            $item = User::model()->findByAttributes(array($attr => $value));
            if ($item != null && $item->id != $this->id) {
                $this->addError($attr, $msg . '重复.');
            }
        }
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {

        $criteria = $this->createDbCriteria();
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    protected function createDbCriteria()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('role', $this->role);
        $criteria->compare('header', $this->header, true);
        $criteria->compare('create_time', $this->create_time);

        return $criteria;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRoleName()
    {
        return User::$USER_ROLES[$this->role];
    }

    public function getAddressName()
    {
        return Yii::app()->user->getIsSuperAdmin() ? '' : $this->address->name;
    }

    public function getBalance()
    {
        $charge = Charge::model()->findBySql('select balance from charge where user_id=' . $this->id . ' order by id desc');
        return $charge['balance'];
    }
}
