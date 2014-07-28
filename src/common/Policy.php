<?php

/**
 * This is the model class for table "policy".
 *
 * The followings are the available columns in table 'policy':
 * @property string $id
 * @property string $name
 * @property string $login_page
 * @property string $portal_page
 * @property integer $expire_time
 * @property integer $is_default
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Node[] $nodes
 */
class Policy extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'policy';
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
			array('expire_time, is_default, create_time', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
                        array('login_page, portal_page', 'length', 'max'=>500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name,login_page,portal_page, expire_time, is_default, create_time', 'safe', 'on'=>'search'),
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
			'nodes' => array(self::HAS_MANY, 'Node', 'policy_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
                        'login_page' => '登陆页面',
                        'portal_page' => '跳转页面',
			'expire_time' => '过期时间，单位秒',
			'is_default' => 'Is Default',
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
		$criteria->compare('name',$this->name,true);
                $criteria->compare('login_page',$this->login_page,true);
                $criteria->compare('portal_page',$this->portal_page,true);
		$criteria->compare('expire_time',$this->expire_time);
		$criteria->compare('is_default',$this->is_default);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Policy the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
