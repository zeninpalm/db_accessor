<?php

/**
 * This is the model class for table "app_service_config".
 *
 * The followings are the available columns in table 'app_service_config':
 * @property string $id
 * @property string $app_id
 * @property string $service_id
 * @property integer $status
 * @property string $hour_max_request
 * @property string $daily_max_request
 * @property string $hour_request
 * @property string $daily_request
 *
 * The followings are the available model relations:
 * @property UserApp $app
 * @property Service $service
 */
class AppServiceConfig extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_service_config';
	}

    public static $STATUS_OK = 0;
    public static $STATUS_STOP = 1;
    public static $STATUS_USER_DEL = 2;


    public static function getAppStatus(){
        return array(UserApp::$STATUS_OK=>'正常',UserApp::$STATUS_STOP=>'超过调用限制',UserApp::$STATUS_USER_DEL=>'禁用');
    }

    public function getStatusName(){
        $appStatus = UserApp::getAppStatus();
        return $appStatus[$this->status];
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
            array('hour_max_request, daily_max_request','required'),
			array('app_id, service_id, hour_max_request, daily_max_request, hour_request, daily_request', 'length', 'max'=>20),
            array('service_id','isDuplicatedConfig'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, app_id, service_id, status, hour_max_request, daily_max_request, hour_request, daily_request', 'safe', 'on'=>'search'),
		);
	}

    public function beforeSave(){
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->hour_request = 0;
                $this->daily_request = 0;
            }
            return true;
        }
        return false;
    }

    public function isDuplicatedConfig(){
        if (!$this->hasErrors()) {
            $config = AppServiceConfig::model()->findByAttributes(array('app_id'=>$this->app_id,'service_id'=>$this->service_id));
            if($config!=null){
                $this->addError('service_id','重复的服务配置');
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
			'app' => array(self::BELONGS_TO, 'UserApp', 'app_id'),
			'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'app_id' => '应用',
			'service_id' => '服务',
			'status' => '状态',
			'hour_max_request' => '小时最大请求(0无限制)',
			'daily_max_request' => '天最大请求(0无限制)',
			'hour_request' => '当前请求(小时)',
			'daily_request' => '当前请求(天)',
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
		$criteria->compare('app_id',$this->app_id,true);
		$criteria->compare('service_id',$this->service_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('hour_max_request',$this->hour_max_request,true);
		$criteria->compare('daily_max_request',$this->daily_max_request,true);
		$criteria->compare('hour_request',$this->hour_request,true);
		$criteria->compare('daily_request',$this->daily_request,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AppServiceConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
