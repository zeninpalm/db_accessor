<?php

/**
 * This is the model class for table "user_beta".
 *
 * The followings are the available columns in table 'user_beta':
 * @property string $id
 * @property string $user_id
 * @property string $beta_id
 * @property integer $status
 * @property integer $update_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property BetaFunction $beta
 * @property User $user
 */
class UserBeta extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_beta';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, beta_id', 'required'),
			array('status, update_time, create_time', 'numerical', 'integerOnly'=>true),
			array('user_id, beta_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, beta_id, status, update_time, create_time', 'safe', 'on'=>'search'),
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
			'beta' => array(self::BELONGS_TO, 'BetaFunction', 'beta_id'),
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
			'user_id' => 'User',
			'beta_id' => 'Beta',
			'status' => '0 - 未开放
1 - 内测开放
2 - 公测开放
3 - 发布开放',
			'update_time' => 'Update Time',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('beta_id',$this->beta_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('update_time',$this->update_time);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserBeta the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
