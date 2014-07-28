<?php

/**
 * This is the model class for table "router".
 *
 * The followings are the available columns in table 'router':
 * @property string $id
 * @property string $user_id
 * @property string $company_id
 * @property string $device_id
 * @property string $notes
 * @property integer $stop_sms
 * @property integer $stop_auth
 * @property integer $commercial
 * @property string $node
 * @property string $address_id
 * @property string $address_detail
 * @property double $latitude
 * @property double $longitude
 * @property integer $create_time
 * @property integer $number
 *
 * The followings are the available model relations:
 * @property Address $address
 * @property Company $company
 * @property Device $device
 * @property User $user
 */
class Router extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'router';
	}
        
        public function defaultScope(){
            if(Yii::app()->user->getIsAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'left join company on company.id = company_id';
                $criteria->addInCondition('company.address_id',Yii::app()->user->getAddressIds());
                return $criteria;
            }
            return array();
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('node', 'required'),
			array('stop_sms, stop_auth, commercial, create_time, number', 'numerical', 'integerOnly'=>true),
			array('latitude, longitude', 'numerical'),
			array('user_id, company_id, device_id, address_id', 'length', 'max'=>20),
			array('node', 'length', 'max'=>45),
                        array('notes', 'length', 'max'=>200),
			array('address_detail', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, company_id, device_id, stop_sms, stop_auth, node, notes, address_id, address_detail, latitude, longitude, create_time, number', 'safe', 'on'=>'search'),
		);
	}
        
        
        private function isRepeat($attr,$value,$msg){
            if (count($this->getErrors($attr))==0) {
                $item = Router::model()->findByAttributes(array($attr=>$value));
                if($item!=null && $item->id != $this->id){
                    $this->addError($attr, $msg.'重复.');
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
			'address' => array(self::BELONGS_TO, 'Address', 'address_id'),
			'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
			'device' => array(self::BELONGS_TO, 'Device', 'device_id'),
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => '创建用户',
			'company_id' => '所属企业',
			'device_id' => '设备',
			'stop_sms' => '强制短信验证',
                        'stop_auth' => '开启信息收集',
                        'commercial' => '启动商用',
			'node' => '网关id',
                        'notes' => '备注',
			'address_id' => '地址区域',
			'address_detail' => '详细地址',
			'latitude' => '纬度',
			'longitude' => '经度',
			'create_time' => '添加日期',
                       'number' => '设备数量',
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
		$criteria->compare('device_id',$this->device_id,true);
		$criteria->compare('stop_sms',$this->stop_sms);
                $criteria->compare('stop_auth',$this->stop_auth);
		$criteria->compare('node',$this->node,true);
                $criteria->compare('notes',$this->notes,true);
		$criteria->compare('address_id',$this->address_id,true);
		$criteria->compare('address_detail',$this->address_detail,true);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('create_time',$this->create_time);
                $criteria->compare('number',$this->number);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Router the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public static function findRouterByNode($identity) {
                return Router::model()->findByAttributes(array('node'=>$identity));
        }

}
