<?php

/**
 * This is the model class for table "connection_data".
 *
 * The followings are the available columns in table 'connection_data':
 * @property integer $id
 * @property integer $view_count
 * @property integer $view_mobile
 * @property integer $login_count
 * @property integer $login_mobile
 * @property integer $new_member
 * @property integer $stat_time
 * @property string $company_id
 *
 * The followings are the available model relations:
 * @property Company $company
 */
class ConnectionData extends CActiveRecord
{
    /* for statistic */
    public $showsTimes;
    public $newUsers;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'connection_data';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('stat_time, company_id', 'required'),
			array('view_count, view_mobile, login_count, login_mobile, new_member, stat_time', 'numerical', 'integerOnly'=>true),
			array('company_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, view_count, view_mobile, login_count, login_mobile, new_member, stat_time, company_id', 'safe', 'on'=>'search'),
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
        
        public function defaultScope(){
            if(Yii::app()->user->getIsShopper()) {
                $criteria = new CDbCriteria;
                $criteria->addInCondition('company_id',Yii::app()->user->getCompanyIds());
                return $criteria;
            }else if(Yii::app()->user->getIsAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'LEFT JOIN company ON company.id=t.company_id';
                $criteria->addInCondition('company.address_id',Yii::app()->user->getAddressIds());
                return $criteria;
            }else if(Yii::app()->user->getIsSuperAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'LEFT JOIN company ON company.id=t.company_id';
                 return $criteria;
            }
            return array();
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'view_count' => '到访次数',
			'view_mobile' => '到访手机号',
			'login_count' => '登陆次数',
			'login_mobile' => '登陆会员数',
			'new_member' => '新增会员数',
			'stat_time' => '统计时间',
			'company_id' => '店铺名称',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('view_count',$this->view_count);
		$criteria->compare('view_mobile',$this->view_mobile);
		$criteria->compare('login_count',$this->login_count);
		$criteria->compare('login_mobile',$this->login_mobile);
		$criteria->compare('new_member',$this->new_member);
		$criteria->compare('stat_time',$this->stat_time);
		$criteria->compare('company_id',$this->company_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConnectionData the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        /**
         * 根据时间统计数据
         * @param int $starttime
         * @param int $endtime
         * @param int $comId
         * @return array
         */
        public static function sumDataBy1($starttime, $endtime, $comId='') {
            $criteria = new CDbCriteria;
            $criteria->addBetweenCondition('stat_time', $starttime, $endtime);
            if($comId) {
                $criteria->addCondition('company_id=:comId');
                $criteria->params[':comId'] = $comId;
            }
            //计算总和
            $criteria->select = 'sum(view_count) as view_count, sum(view_mobile) as view_mobile, sum(login_count) as login_count,
                                    sum(login_mobile) as login_mobile, sum(new_member) as new_member';
            $criteria->limit = 1;
            return ConnectionData::model()->find($criteria);
        }
        public static function sumDataBy($starttime, $endtime, $addressId,$companyCategoryId) {
            $criteria = new CDbCriteria;
            $criteria->addBetweenCondition('stat_time', $starttime, $endtime);
            if($addressId!=null&&$addressId!=0) {
                $address = Address::model()->findByPk($addressId);
                $addressIds = $address->getIdsWithSub();
                $criteria->addInCondition('address_id', $addressIds);
            }
            if($companyCategoryId!=null&&$companyCategoryId!=0){
                $criteria->addCondition('category_id='.$companyCategoryId);
            }
            //计算总和
            $criteria->select = 'sum(view_count) as view_count, sum(view_mobile) as view_mobile, sum(login_count) as login_count,
                                    sum(login_mobile) as login_mobile, sum(new_member) as new_member';
            $criteria->limit = 1;
            return ConnectionData::model()->find($criteria);
        }
        
}
