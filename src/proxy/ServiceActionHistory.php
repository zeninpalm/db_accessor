<?php

/**
 * This is the model class for table "service_action_history".
 *
 * The followings are the available columns in table 'service_action_history':
 * @property string $id
 * @property string $app_id
 * @property string $service_action_id
 * @property string $header
 * @property string $body
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property UserApp $app
 * @property ServiceAction $serviceAction
 */
class ServiceActionHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'service_action_history';
	}

    public function beforeSave(){
        if(parent::beforeSave()){
            if($this->isNewRecord){
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
			array('create_time', 'numerical', 'integerOnly'=>true),
			array('app_id, service_action_id', 'length', 'max'=>20),
			array('header, body', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, app_id, service_action_id, header, body, create_time', 'safe', 'on'=>'search'),
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
			'app' => array(self::BELONGS_TO, 'UserApp', 'app_id'),
			'serviceAction' => array(self::BELONGS_TO, 'ServiceAction', 'service_action_id'),
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
			'service_action_id' => '服务API',
			'header' => '头信息',
			'body' => '正文内容',
			'create_time' => '添加日期',
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
		$criteria->compare('service_action_id',$this->service_action_id,true);
		$criteria->compare('header',$this->header,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ServiceActionHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
