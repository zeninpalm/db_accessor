<?php

/**
 * This is the model class for table "address".
 *
 * The followings are the available columns in table 'address':
 * @property string $id
 * @property string $name
 * @property string $parent_id
 * @property double $latitude
 * @property double $longitude
 *
 * The followings are the available model relations:
 * @property Address $parent
 * @property Address[] $addresses
 * @property Company[] $companies
 * @property Router[] $routers
 * @property Support $support
 * @property User[] $users
 */
class Address extends CActiveRecord
{
    
        public $province;
        
        public $city;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('latitude, longitude', 'numerical'),
                        array('name', 'required'),
			array('name', 'length', 'max'=>100),
			array('parent_id', 'length', 'max'=>20),
                        array('name', 'isRepeatName'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, parent_id, latitude, longitude', 'safe', 'on'=>'search'),
		);
	}
}

