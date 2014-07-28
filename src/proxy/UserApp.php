<?php

/**
 * This is the model class for table "user_app".
 *
 * The followings are the available columns in table 'user_app':
 * @property string $id
 * @property integer $status
 * @property string $user_id
 * @property string $name
 * @property string $url
 * @property string $permission_level
 * @property string $app_sn
 * @property string $app_token
 * @property integer $app_category_id
 * @property string $description
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property AppServiceConfig[] $appServiceConfigs
 * @property ServiceActionHistory[] $serviceActionHistories
 * @property AppCategory $appCategory
 * @property User $user
 */
class UserApp extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_app';
	}

    public static $STATUS_OK = 0;
    public static $STATUS_STOP = 1;
    public static $STATUS_USER_DEL = 2;


    public static function getAppStatus(){
        return array(UserApp::$STATUS_OK=>'正常',UserApp::$STATUS_STOP=>'停止',UserApp::$STATUS_USER_DEL=>'用户删除');
    }

    public function getStatusName(){
        $appStatus = UserApp::getAppStatus();
        return $appStatus[$this->status];
    }

    public function beforeSave(){
        if(parent::beforeSave()){
            if($this->isNewRecord){
                $this->create_time = time();
                $this->permission_level = 0;
                $this->status = UserApp::$STATUS_STOP;
                $this->app_sn = substr(time(),3).rand(100,999);
                $this->app_token = sha1('NCD*34ks@#)('.time().'CDK@(D)(N');
                $this->status = UserApp::$STATUS_STOP;
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
			array('user_id, name, app_category_id', 'required'),
			array('status, app_category_id, create_time', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>20),
			array('name', 'length', 'max'=>50),
            array('name', 'unique','message'=>'应用名称存在 不可重复创建.'),
			array('url', 'length', 'max'=>200),
			array('permission_level', 'length', 'max'=>45),
			array('app_sn', 'length', 'max'=>40),
			array('app_token', 'length', 'max'=>100),
            array('description', 'length', 'max'=>1000),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, status, user_id, name, url, permission_level, app_sn, app_token, app_category_id, description, create_time', 'safe', 'on'=>'search'),
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
			'appServiceConfigs' => array(self::HAS_MANY, 'AppServiceConfig', 'app_id'),
			'serviceActionHistories' => array(self::HAS_MANY, 'ServiceActionHistory', 'app_id'),
			'appCategory' => array(self::BELONGS_TO, 'AppCategory', 'app_category_id'),
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
			'status' => '是否有效',
			'user_id' => 'User',
			'name' => '应用名称',
			'url' => '应用地址',
			'permission_level' => '权限',
			'app_sn' => '序列号',
			'app_token' => '授权Token',
			'app_category_id' => '应用类型',
            'description' => '简介',
			'create_time' => '创建日期',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('permission_level',$this->permission_level,true);
		$criteria->compare('app_sn',$this->app_sn,true);
		$criteria->compare('app_token',$this->app_token,true);
		$criteria->compare('app_category_id',$this->app_category_id);
        $criteria->compare('description',$this->description,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserApp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
