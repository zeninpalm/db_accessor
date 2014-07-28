<?php
/**
 * Created by PhpStorm.
 * User: wei
 * Date: 14-7-28
 * Time: 下午3:10
 */
require_once __DIR__ . '/../common/User.php';

class AdUser extends User
{

    /**
     * This is the model class for table "user".
     *
     * The followings are the available columns in table 'user':
     * @property string $create_time
     */

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'mobile' => '手机号',
            'name' => '名称',
            'password' => '密码',
            'email' => '邮箱',
            'header' => '头像',
            'create_time' => '注册日期',
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
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
            array('id, mobile, name, email, header, create_time', 'safe', 'on'=>'search'),
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
        );
    }

    public function isRepeatMobile(){
        if($this->mobile!=null&& strlen($this->mobile)>0){
            if(!preg_match(UserIdentity::$PHONE_MATCH, $this->mobile)){
                $this->addError('mobile', '请输入正确的手机号');
            }else{
                parent::isRepeat('mobile', $this->mobile, '手机号');
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