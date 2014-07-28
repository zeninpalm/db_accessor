<?php

/**
 * This is the model class for table "company_category".
 *
 * The followings are the available columns in table 'company_category':
 * @property string $id
 * @property string $name
 * @property string $parent_id
 *
 * The followings are the available model relations:
 * @property Company[] $companies
 * @property CompanyCategory $parentCategory 
 * @property CompanyCategory[] $children
 */
class CompanyCategory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'company_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>40),
			array('parent_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, parent_id', 'safe', 'on'=>'search'),
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
			'companies' => array(self::HAS_MANY, 'Company', 'category_id'),
                        'parentCategory' => array(self::BELONGS_TO,'CompanyCategory','parent_id'),
                        'children' => array(self::HAS_MANY,'CompanyCategory','parent_id')
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
			'parent_id' => '父类',
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
		$criteria->compare('parent_id',$this->parent_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public static function parentCategories(){
            return CompanyCategory::model()->findAllByAttributes(array('parent_id'=>0));
        }
        
        public static function childrenCategories($parentId=-1){
            return CompanyCategory::model()->findAllByAttributes(array('parent_id'=>$parentId));
        }

        public static function loadFirstCategoryId() {
            $cat = CompanyCategory::model()->findByAttributes(array('parent_id'=>0));
            return $cat->id;
        }
        
        public function getChildrenIds(){
            $results = array($this->id);
            foreach ($this->children as $tmpAddr) {
                $results = array_merge($results,$tmpAddr->getChildrenIds());
            }
            return $results;
        }

}
