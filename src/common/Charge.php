<?php

/**
 * This is the model class for table "charge".
 *
 * The followings are the available columns in table 'charge':
 * @property string $id
 * @property string $user_id
 * @property string $operator_id
 * @property integer $type
 * @property double $cost
 * @property double $balance
 * @property string $desc
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property User $user
 * @property User $operator
 */
class Charge extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $state;
        public static $TYPE =array('充值','消费');
         public function defaultScope(){
            if(Yii::app()->user->getIsShopper()) {
                return array(
                    'condition'=>"user_id = :userId",
                    'params'=>array(':userId'=>Yii::app()->user->getId()),
                );
            }else if(Yii::app()->user->getIsAdmin()){
                $criteria = new CDbCriteria;
                $criteria->with ='user';
                $criteria->addInCondition('address_id',Yii::app()->user->getAddressIds());
                return $criteria;
            }
            return array();
        }
        public function tableName()
	{
		return 'charge';
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
			array('user_id', 'required'),
			array('type, create_time', 'numerical', 'integerOnly'=>true),
			array('cost, balance', 'numerical'),
			array('user_id, operator_id', 'length', 'max'=>20),
			array('desc', 'length', 'max'=>500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, operator_id, type, cost, balance, desc, create_time', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
			'operator' => array(self::BELONGS_TO, 'User', 'operator_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => '商户',
			'operator_id' => '操作人员',
			'type' => '1 ：充值   2：消费',
			'cost' => '金额',
			'balance' => '当前余额',
			'desc' => '备注',
			'create_time' => '记录日期',
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
		$criteria->compare('operator_id',$this->operator_id,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('cost',$this->cost);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Charge the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
