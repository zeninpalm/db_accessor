<?php

/**
 * This is the model class for table "service".
 *
 * The followings are the available columns in table 'service':
 * @property string $id
 * @property integer $status
 * @property string $name
 * @property string $identity
 * @property string $access_url
 * @property string $token
 * @property string $description
 *
 * The followings are the available model relations:
 * @property AppServiceConfig[] $appServiceConfigs
 * @property ServiceAction[] $serviceActions
 */
class Service extends CActiveRecord
{

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'service';
	}

    public static $STATUS_OK = 0;
    public static $STATUS_CLOSE = 1;

    public static function getServiceStatus(){
        return array(Service::$STATUS_OK=>'正常',Service::$STATUS_CLOSE=>'关闭');
    }

    public function getStatusName(){
        $serviceStatus = Service::getServiceStatus();
        return $serviceStatus[$this->status];
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			array('identity, name', 'length', 'max'=>50),
			array('access_url, token', 'length', 'max'=>200),
            array('description', 'length', 'max'=>1000),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, name, identity, access_url, token , description', 'safe', 'on'=>'search'),
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
			'appServiceConfigs' => array(self::HAS_MANY, 'AppServiceConfig', 'service_id'),
			'serviceActions' => array(self::HAS_MANY, 'ServiceAction', 'service_id', 'order'=>'serviceActions.id ASC'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'status' => '状态',
			'name' => '名称',
			'identity' => '识别码',
			'access_url' => '访问地址',
			'token' => 'Token',
            'description' => '简介',
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
		$criteria->compare('status',$this->status);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('identity',$this->identity,true);
		$criteria->compare('access_url',$this->access_url,true);
		$criteria->compare('token',$this->token,true);
        $criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Service the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
