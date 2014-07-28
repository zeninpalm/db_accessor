<?php

require_once __DIR__ . '/../common/ConnectionUser.php';

class SjConnectionUser extends ConnectionUser {
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

    public function rules() {
    	$rules = parent::rules();
    	$rules[1] = array(
    		'router_id,company_id, status, add_time, update_time, sms_verified', 
    		'numerical', 
    		'integerOnly'=>true
    	),
    	$rules[5] = array(
    		array('id, mobile, mac, node, router_id,company_id, url, veri_code, status, add_time, update_time', 
    			'safe', 
    			'on'=>'search'),
    	);
    	return $rules;
    }

    public function relations() {
    	$relations = parent::relations();
    	$additionals = array(
    		'router' => array(self::BELONGS_TO, 'Router', 'router_id'),
    	);
    	return array_merge($relations, $additionals);
    }

    public function attributeLabels() {
    	$labels = parent::attributeLabels();
    	$additionals = array(
    		'router_id' => '网关id',
    	);
    	return array_merge($labels, $additionals);
    }

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

    public function getBanner(){
        if($this->router!=null){
            return $this->router->company->logo;
        }
        return '/images/p1-banner.jpg';
    }
}