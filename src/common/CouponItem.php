<?php

/**
 * This is the model class for table "coupon_item".
 *
 * The followings are the available columns in table 'coupon_item':
 * @property string $id
 * @property string $coupon_id
 * @property string $mobile
 * @property string $company_id
 * @property string $unique_code
 * @property integer $status
 * @property integer $verify_time
 * @property string $mac
 * @property string $ip_address
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Coupon $coupon
 * @property Router $router
 */
class CouponItem extends CActiveRecord
{
    
        public static $STATUS_NOT_SEND = 0;
        
        public static $STATUS_SEND = 1;
        
        public static $STATUS_USED = 2;
        
        public static $STATUS_EXPIRED = 3;

        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coupon_item';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, verify_time, create_time', 'numerical', 'integerOnly'=>true),
			array('coupon_id, mobile, company_id', 'length', 'max'=>20),
			array('unique_code', 'length', 'max'=>45),
			array('mac', 'length', 'max'=>50),
			array('ip_address', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, coupon_id, mobile, router_id, unique_code, status, verify_time, mac, ip_address, create_time', 'safe', 'on'=>'search'),
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
			'coupon' => array(self::BELONGS_TO, 'Coupon', 'coupon_id'),
			'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'coupon_id' => '奖券',
			'mobile' => 'Mobile',
			'company_id' => '店铺',
			'unique_code' => '认证码',
			'status' => '状态。0：未发出，1：已发出，2:已使用，3：过期',
			'verify_time' => 'Verify Time',
			'mac' => 'Mac',
			'ip_address' => 'Ip Address',
			'create_time' => 'Create Time',
		);
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
		$criteria->compare('coupon_id',$this->coupon_id,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('company_id',$this->compamy_id,true);
		$criteria->compare('unique_code',$this->unique_code,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('verify_time',$this->verify_time);
		$criteria->compare('mac',$this->mac,true);
		$criteria->compare('ip_address',$this->ip_address,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CouponItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getStatusName(){
            switch ($this->status) {
            case CouponItem::$STATUS_EXPIRED: return '过期';
            case CouponItem::$STATUS_NOT_SEND: return '未发送';
            case CouponItem::$STATUS_SEND: return '已发送';
            case CouponItem::$STATUS_USED: return '已使用';
            default :return '未知错误';
        }
    }
}
