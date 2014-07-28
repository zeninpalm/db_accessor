<?php

/**
 * This is the model class for table "company_img".
 *
 * The followings are the available columns in table 'company_img':
 * @property string $id
 * @property string $company_id
 * @property string $img
  * @property string $img_id
 * @property integer $status
 * @property string $type
 * @property string $sort
 * 
 * The followings are the available model relations:
 * @property Company $company
 */
class CompanyImg extends CActiveRecord
{
    
        public static $IMG_TYPE = array('logo'=>'LOGO图片','ad'=>'广告图片');
        
        public static $IMG_STATUS = array(0=>'无效',1=>'生效');
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'company_img';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('company_id, img', 'required'),
			array('company_id', 'length', 'max'=>20),
			array('img', 'file','types'=>'jpg, png', 'maxSize'=>1024*200, 'allowEmpty'=>true, 'tooLarge'=>'图片大小不能超过200KB'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, company_id, sort, img, type, status', 'safe', 'on'=>'search'),
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
			'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'company_id' => '公司id',
			'img' => '图片资源',
                        'status' => '状态',
			'type' => '图片类型（logo或ad）',
                    'sort'=>'排序',
		);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyImg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getStatusText(){
            return $this->status==0? '<span class="red">无效</span>' : '<span class="green">生效</span>';
        }
        
        /**
         * 获取商家图片
         * @param int $companyId
         * @param type $type
         */
        public static function getImg($companyId, $type='logo') {
            $companyImg = CompanyImg::model()->findByAttributes(array('company_id'=>$companyId,'type'=>$type));
            return $companyImg ? $companyImg->img : '';
        }
}
