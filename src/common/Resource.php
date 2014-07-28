<?php

/**
 * This is the model class for table "resource".
 *
 * The followings are the available columns in table 'resource':
 * @property string $id
 * @property string $advertisement_id
 * @property string $key
 * @property integer $source_id
 * @property string $source_path
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Advertisement $advertisement
 */
class Resource extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'resource';
	}

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
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
			array('source_id, create_time', 'numerical', 'integerOnly'=>true),
			array('advertisement_id', 'length', 'max'=>20),
			array('key', 'length', 'max'=>50),
			array('source_path', 'length', 'max'=>300),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, advertisement_id, key, source_id, source_path, create_time', 'safe', 'on'=>'search'),
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
			'advertisement' => array(self::BELONGS_TO, 'Advertisement', 'advertisement_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'advertisement_id' => 'Advertisement',
			'key' => '用于替换模板中的内容',
			'source_id' => '资源库唯一ID',
			'source_path' => '资源URL',
			'create_time' => '添加时间',
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
		$criteria->compare('advertisement_id',$this->advertisement_id,true);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('source_id',$this->source_id);
		$criteria->compare('source_path',$this->source_path,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Resource the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
