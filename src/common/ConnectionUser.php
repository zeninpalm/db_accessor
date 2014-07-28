<?php

/**
 * This is the model class for table "connection_user".
 *
 * The followings are the available columns in table 'connection_user':
 * @property integer $id
 * @property string $mobile
 * @property string $mac
 * @property string $node
 * @property integer $sms_verified
 * @property integer $router_id
 * @property string $url
 * @property string $veri_code
 * @property integer $status
 * @property integer $add_time
 * @property integer $update_time
 * @property integer $company_id
 * 
 * @property Router $router
 */
class ConnectionUser extends CActiveRecord
{
        public $mac;
        public $address;
        public $port;
    /**
     * for stat
     */
    public $statCount;
    public $statTime;
        
        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->add_time = time();
                }
                return true;                
            }
            return false;
        }
        
        public function defaultScope(){
            if(Yii::app()->user->getIsAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'left join company on company.id=company_id';
                $criteria->addInCondition('company.address_id',Yii::app()->user->getAddressIds());
                return $criteria;
            }
            if(Yii::app()->user->getIsSuperAdmin()){
                $criteria = new CDbCriteria;
                $criteria->join = 'left join company on company.id=company_id';
                return $criteria;
            }
            return array();
        }
        
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'connection_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('mobile, mac, node', 'required'),
			array('router_id,company_id, status, add_time, update_time, sms_verified', 'numerical', 'integerOnly'=>true),
			array('mobile', 'length', 'max'=>13),
			array('mac, node, veri_code', 'length', 'max'=>45),
			array('url', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, mobile, mac, node, router_id,company_id, url, veri_code, status, add_time, update_time', 'safe', 'on'=>'search'),
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
                    'router' => array(self::BELONGS_TO, 'Router', 'router_id'),
                    'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '主键id',
			'mobile' => '手机号码',
			'mac' => 'MAC地址',
			'node' => '路由器网关id',
			'router_id' => '网关id',
                        'company_id' => '店铺id',
                        'sms_verified' => '是否验证',
			'url' => '客户端初始URL',
			'veri_code' => '验证码',
			'status' => '状态 0:未验证，1：已验证；2：已过期',
			'add_time' => '添加时间',
			'update_time' => '更新时间',
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
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('mac',$this->mac,true);
		$criteria->compare('node',$this->node,true);
		$criteria->compare('router_id',$this->router_id);
                $criteria->compare('company_id',$this->company_id);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('veri_code',$this->veri_code,true);
                $criteria->compare('sms_verified',$this->sms_verified,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('add_time',$this->add_time);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConnectionUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

        public static function parseGateWay($address,$port,$identity,$url,$mac) {
                $tmp = new ConnectionUser;
                $tmp->url = $url;
                $tmp->node = $identity;
                $tmp->router =  Router::findRouterByNode($identity);
                $tmp->router_id = $tmp->router!=null?$tmp->router->id:0;
                $tmp->company = Company::model()->findByAttributes(array('node'=>$identity));
                $tmp->company_id = $tmp->company!=null?$tmp->company->id:0;
                $tmp->sms_verified = 1;
                $tmp->mac = $mac;
                $tmp->port = $port;
                $tmp->address = $address;
                return $tmp;
        }
        
        public function isValidAccess(){
                if ($this->node==null || $this->mac==NULL || $this->address == null || $this->port == null){
                    return false;
                }
                return true;
        }
        
        /**
         * Will display verify code after 60s if stop SMS.
         * @return boolean
         */
        public function isStopSMS(){
            if($this->router!=null && $this->router->stop_sms){
                return true;
            }
            return false;
        }
        
        public function isStopAuth(){
            if($this->router!=null && $this->router->stop_auth){
                return true;
            }
            return false;
        }

        public function getAuthPuppyQuery(){
            return http_build_query(array('gw_address'=>$this->address,'gw_port'=>$this->port,'gw_id'=>$this->node,'mac'=>$this->mac));
        }

        public function getBanner(){
            if($this->router!=null){
                return $this->router->company->logo;
            }
            return '/images/p1-banner.jpg';
        }
}
