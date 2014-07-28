<?php

/**
 * This is the model class for table "coupon".
 *
 * The followings are the available columns in table 'coupon':
 * @property string $id
 * @property string $user_id
 * @property string $company_id
 * @property string $name
 * @property string $img_id
 * @property string $img
 * @property string $bg_color
 * @property string $content
 * @property string $notes
 * @property string $view_time
 * @property integer $number
 * @property integer $left_num
 * @property integer $evaluate
 * @property integer $use_num
 * @property integer $type
 * @property double $value
 * @property integer $status
 * @property integer $start_time
 * @property integer $create_time
 * @property integer $expire_time
 *
 * The followings are the available model relations:
 * @property Company $company
 * @property User $user
 * @property CouponItem[] $couponItems
 */
class Coupon extends CActiveRecord
{
    
        public static $TYPE_FREE_GET = 0;
        public static $TYPE_ROLL_GET = 1;
        public static $TYPE_NO_VALUE = 2;
        
        public static $STATUS_CLOSE = 0;
        public static $STATUS_OK = 1;
        public static $STATUS_APPROVING = 2;
        public static $STATUS_REJECT = 3;
        
        public static function getTypes($needNoValueType = false){
            $result = array(
                Coupon::$TYPE_FREE_GET=>'超值团购',
                Coupon::$TYPE_ROLL_GET=>'幸运抽奖',
            );
            return $needNoValueType? array_merge($result, array(Coupon::$TYPE_NO_VALUE=>'无效奖品',)) : $result;
        }
        
        public function defaultScope(){
            if(Yii::app()->user->getIsShopper()) {
                $criteria = new CDbCriteria;
                $criteria->addInCondition('company_id',Yii::app()->user->getCompanyIds());
                return $criteria;
            }else if(Yii::app()->user->getIsAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'left join company on company.id=company_id';
                $criteria->addInCondition('address_id',Yii::app()->user->getAddressIds());
                return $criteria;
            }
            return array();
        }
        
        public function getTypeText(){
            switch($this->type){
                case Coupon::$TYPE_FREE_GET:
                    return '超值团购';
                case Coupon::$TYPE_ROLL_GET:
                    return '幸运抽奖';
                case Coupon::$TYPE_NO_VALUE:
                    return '无效奖品';
            }
        }
        
        public static function getBgColors(){
            return array(
                '#65d698'=>'绿色',
                '#63b8ff'=>'蓝色',
                '#9f79ee'=>'紫色',
            );
        }
        
        public function getStatusText(){
            switch($this->status){
                case Coupon::$STATUS_CLOSE:
                    return '关闭';
                case Coupon::$STATUS_OK:
                    return '正常';
                case Coupon::$STATUS_APPROVING:
                    return '审核中';
                case Coupon::$STATUS_REJECT:
                    return '拒绝';
            }
        }


        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coupon';
	}

        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->create_time = time();
                    $this->view_time = 0;
                    $this->left_num = 0;
                    $this->left_num = $this->number;
                    $this->status = Coupon::$STATUS_APPROVING;
                    $types = Coupon::getTypes();
                    $this->name = $this->name==null||$this->name==''? $types[$this->type].' '.$this->value : $this->name;
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
			array('number,start_time, expire_time, value', 'required'),
			array('number, left_num, evaluate, use_num, type, status, create_time,start_time, expire_time', 'numerical', 'integerOnly'=>true),
                        array('cost_price', 'numerical', 'min'=>0),
			array('value', 'numerical', 'min'=>5),
                        array('number', 'numerical', 'min'=>10),
			array('user_id, company_id, img_id, view_time', 'length', 'max'=>20),
			array('name', 'length', 'max'=>300),
			array('img', 'length', 'max'=>500),
                        array('bg_color', 'length', 'max'=>20),
			array('content', 'safe'),
                        array('start_time','isRightStartTime','on'=>'create'),
                        array('expire_time','isRightExpireTime'),
                        array('name','isRightName'),
                        array('company_id','isRightCompany'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, company_id, name, img_id, img, bg_color, content, notes, view_time, number, left_num, evaluate, use_num, type, value,cot_price, status, create_time, start_time, expire_time', 'safe', 'on'=>'search'),
		);
	}

        public function isRightStartTime(){
            if (count($this->getErrors('start_time'))==0) {
                if($this->start_time < time()){
                    $this->addError('start_time', '开始时间必须晚于今天');
                }
            }
        }
        
        public function isRightName(){
            if (count($this->getErrors('name'))==0) {
                if($this->type == Coupon::$TYPE_NO_VALUE && ($this->name == null || $this->name == '')){
                    $this->addError('name', '无效奖卷必须填写名称');
                }
            }
        }
        
        public function isRightCompany(){
            if($this->type != Coupon::$TYPE_NO_VALUE && ($this->company_id == null || $this->company_id == '' || $this->company_id <1)){
                $this->addError('company_id', '请选择一个有效的店铺');
            }
        }

        public function isRightExpireTime(){
            if (count($this->getErrors('start_time'))==0 && count($this->getErrors('expire_time'))==0) {
                if($this->expire_time < time() || $this->expire_time <= $this->start_time){
                    $this->addError('expire_time', '结束时间必须晚于今天并且大于开始时间');
                }
            }
        }
        
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'couponItems' => array(self::HAS_MANY, 'CouponItem', 'coupon_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'id',
			'user_id' => '用户',
			'company_id' => '店铺',
			'name' => '名称',
			'img_id' => '对应图片',
			'img' => 'Img',
                        'bg_color' => '背景颜色',
			'content' => '内容',
			'view_time' => '展示次数',
			'number' => '总数量(用户最大可以获得的数量)',
			'left_num' => '剩余',
                        'notes' => '备注',
			'evaluate' => '评价',
			'use_num' => '已使用',
			'type' => '获得方式',
			'value' => '奖券价值(单位:元)',
                        'cost_price' => '原价(单位:元)',
			'status' => '状态',
                        'start_time' => '开始时间',
			'create_time' => '创建时间',
			'expire_time' => '结束时间',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('company_id',$this->company_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('img_id',$this->img_id,true);
		$criteria->compare('img',$this->img,true);
                $criteria->compare('bg_color',$this->bg_color,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('view_time',$this->view_time,true);
		$criteria->compare('number',$this->number);
		$criteria->compare('left_num',$this->left_num);
		$criteria->compare('evaluate',$this->evaluate);
		$criteria->compare('use_num',$this->use_num);
                $criteria->compare('notes',$this->notes);
		$criteria->compare('type',$this->type);
		$criteria->compare('value',$this->value);
                $criteria->compare('cost_price',$this->cost_price);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time);
                $criteria->compare('start_time',$this->start_time);
		$criteria->compare('expire_time',$this->expire_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Coupon the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public static function getNoValueCoupons($limit) {
            $criteria = new CDbCriteria;
            $criteria->addCondition('type=' . Coupon::$TYPE_NO_VALUE); //表示抽奖
            $criteria->addCondition('expire_time>' . time()); //没有过期
            $criteria->addCondition('status=' . Coupon::$STATUS_OK); //已经生效
            $criteria->limit = $limit;
            $data = new CActiveDataProvider('coupon', array('criteria' => $criteria));
            return $data->getData();
        }
        
        public function timeFieldToStr(){
            if($this->start_time!=null && $this->start_time > 100){
                $this->start_time = date('Y-m-d', $this->start_time);
            }
            if($this->expire_time!=null && $this->expire_time > 100){
                $this->expire_time = date('Y-m-d', $this->expire_time);
            }
        }
        
        public function timeFieldToTime(){
            if($this->start_time!=null && $this->start_time!=''){
                $this->start_time = strtotime($this->start_time);
            }
            if($this->expire_time!=null && $this->expire_time!=''){
                $this->expire_time = strtotime($this->expire_time);
            }
        }
        public static function getCouponTypes(){
            return array(array('id'=>  self::$TYPE_FREE_GET,'name'=>'超值团购'),
                array('id'=>  self::$TYPE_ROLL_GET,'name'=>'免费抽奖'));
        }
}
