<?php

/**
 * This is the model class for table "service_action".
 *
 * The followings are the available columns in table 'service_action':
 * @property string $id
 * @property string $name
 * @property string $service_id
 * @property string $action_source
 * @property string $action_dest
 * @property integer $create_time
 * @property integer $permission_level
 * @property string $description
 * @property string $doc
 *
 * The followings are the available model relations:
 * @property Service $service
 * @property ServiceActionHistory[] $serviceActionHistories
 */
class ServiceAction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'service_action';
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
            array('permission_level, name, action_source, action_dest, service_id', 'required'),
			array('create_time, permission_level', 'numerical', 'integerOnly'=>true),
			array('name, action_source, action_dest', 'length', 'max'=>50),
			array('service_id', 'length', 'max'=>20),
			array('description, doc', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, service_id, action_source, action_dest, create_time, permission_level, description, doc', 'safe', 'on'=>'search'),
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
			'service' => array(self::BELONGS_TO, 'Service', 'service_id'),
			'serviceActionHistories' => array(self::HAS_MANY, 'ServiceActionHistory', 'service_action_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '名称',
			'service_id' => '服务',
			'action_source' => '源接口',
			'action_dest' => '目标接口',
			'create_time' => '创建日期',
			'permission_level' => '权限等级',
			'description' => '描述',
			'doc' => '文档说明',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('service_id',$this->service_id,true);
		$criteria->compare('action_source',$this->action_source,true);
		$criteria->compare('action_dest',$this->action_dest,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('permission_level',$this->permission_level);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('doc',$this->doc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ServiceAction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
