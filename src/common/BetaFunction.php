<?php

/**
 * This is the model class for table "beta_function".
 *
 * The followings are the available columns in table 'beta_function':
 * @property string $id
 * @property string $name
 * @property string $preview_img
 * @property string $preview_id
 * @property string $description
 * @property string $detail_link
 * @property integer $status
 * @property integer $update_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property UserBeta[] $userBetas
 */
class BetaFunction extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'beta_function';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, update_time, create_time, preview_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
			array('preview_img, detail_link', 'length', 'max'=>200),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, preview_img, description, detail_link, status, update_time, create_time', 'safe', 'on'=>'search'),
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
			'userBetas' => array(self::HAS_MANY, 'UserBeta', 'beta_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '功能名称',
			'preview_img' => '显示图片',
                        'preview_id' => '显示图片资源ID',
			'description' => '功能描述',
			'detail_link' => '详情链接',
			'status' => '0 - 未发布 1 - 内测 2 - 公测 3 - 发布',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('preview_img',$this->preview_img,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('detail_link',$this->detail_link,true);
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
	 * @return BetaFunction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /**
         * 根据属性获取UserBeta值
         * @param integer $user_id
         * @return UserBeta
         */
        public function getUserBeta($user_id) {
            return UserBeta::model()->findByAttributes(array('user_id' => $user_id, 'beta_id' => $this->id));
        }
}
