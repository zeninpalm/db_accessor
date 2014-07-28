<?php

/**
 * This is the model class for table "view_history".
 *
 * The followings are the available columns in table 'view_history':
 * @property string $id
 * @property string $advertisement_id
 * @property integer $is_click
 * @property string $user_id
 * @property string $remote_ip
 * @property string $click_url
 * @property string $user_agent
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Advertisement $advertisement
 */
class ViewHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'view_history';
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
			array('is_click, create_time', 'numerical', 'integerOnly'=>true),
			array('advertisement_id, user_id', 'length', 'max'=>20),
			array('remote_ip', 'length', 'max'=>48),
			array('click_url, user_agent', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, advertisement_id, is_click, user_id, remote_ip, click_url, user_agent, create_time', 'safe', 'on'=>'search'),
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
			'advertisement_id' => '广告',
			'is_click' => '是否点击',
			'user_id' => '手机号',
			'remote_ip' => '浏览IP',
			'click_url' => '点击链接',
			'user_agent' => '浏览器Agent',
			'create_time' => '创建日期',
		);
	}

    public function getAgent(){
        if(strlen($this->user_agent)>10){
            return substr($this->user_agent,0,10);
        }
        return $this->user_agent;
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
		$criteria->compare('is_click',$this->is_click);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('remote_ip',$this->remote_ip,true);
		$criteria->compare('click_url',$this->click_url,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ViewHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
