<?php

require_once __DIR__ . '/../common/Member.php';

class PortalMember extends Member {

  public function defaultScope(){
    if(Yii::app()->user->getIsAdmin()){
      $criteria = new CDbCriteria;
      $criteria->join = 'left join company_member on company_member.member_id=t.id left join company on company.id=company_member.company_id';
      $criteria->addInCondition('company.address_id',Yii::app()->user->getAddressIds());
      return $criteria;
    }
    return array();
  }

  public function rules() {
  	$rules = parent::rules();
  	$rules[0] = array('create_time, dob, gender, header_id', 'numerical', 'integerOnly'=>true);
  	return $rules;
  }	

  public function attributeLabels() {
  	$labels = parent::attributeLabels();
  	$additionals = array(
  		'header_id' => '头像资源ID',
  	);
  	return array_merge($labels, $additionals);
  }

  public function search()
  {
    // @todo Please modify the following code to remove attributes that should not be searched.

    $criteria=new CDbCriteria;

    $criteria->compare('id',$this->id,true);
    $criteria->compare('name',$this->name,true);
    $criteria->compare('email',$this->email,true);
    $criteria->compare('password',$this->password,true);
    $criteria->compare('nick_name',$this->nick_name,true);
    $criteria->compare('mobile',$this->mobile,true);
    $criteria->compare('header',$this->header,true);
    $criteria->compare('dob',$this->dob,true);
    $criteria->compare('signature',$this->signature,true);
    $criteria->compare('create_time',$this->create_time);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  public static function createMemberWithCompany($mobile, $companyId){
    $member = Member::model()->findByAttributes(array('mobile'=>$mobile));
    if($member==null){
      $member = new Member;
      $member->nick_name = $mobile;
      $member->mobile = $mobile;
      $member->password = md5(rand(100000, 999999));
      $member->save();
    }
    CompanyMember::createMember($companyId, $member->id);
  }

  public static function statByDaily($startTime, $endtime, $interval, $timeformat) {
    $chartData = $chart_x = $chart_y = array();
    $chart_y['member'] = $chart_y['active'] = array();

    while($startTime <= $endtime) {
      $nextTime = $startTime + $interval;
      $chart_x[] = date($timeformat, $startTime);
      Member::appendAllUsers($chart_y, $nextTime);
      Member::appendActiveUsers($chart_y, $nextTime, $startTime);
      $startTime = $nextTime;
    }
    $chartData[] = new ChartData($chart_y['member'], '总用户', ChartData::GRAY);
    $chartData[] = new ChartData($chart_y['active'], '活跃用户',  ChartData::BLUE);   
    return array('labels'=>$chart_x,'chartData'=>$chartData);
  }

  private static function appendAllUsers(&$chart_y, $nextTime, $startTime=NULL) {
    Member::append(
      $chart_y,
      "AllUsers",
      "member",
      $nextTime,
      $startTime);
  }

  private static function appendActiveUsers(&$chart_y, $nextTime, $startTime) {
    Member::append(
      $chart_y,
      "ActiveUsers",
      "active",
      $nextTime,
      $startTime);
  }

  private static function append(&$chart_y, $type, $category, $nextTime, $startTime=NULL) {
    $key = (Yii::app()->user->getUser()->name) . $type . (isset($startTime) ? $startTime : '') . $nextTime;
    Member::fetchData(
      $chart_y, $key, Member::getModel($type),
      Member::getStatement($type, $nextTime, $startTime),
      $category,
      Member::getExpiration($nextTime)); 
  }

  private static function getModel($type) {
    if ($type == "ActiveUsers") {
      return ConnectionUser::model();
    } elseif ($type == "AllUsers") {
      return Member::model();
    }
  }

  private static function getStatement($type, $nextTime, $startTime=NULL) {
    if ($type == "ActiveUsers") {
      return array(
        'select'=>'t.id',
        'condition'=>'t.add_time >= :yesterday and t.add_time < :today and sms_verified=1', 
        'distinct'=>true, 
        'params'=>array(':yesterday' => $startTime,
        ':today'=>$nextTime));
    } elseif ($type == "AllUsers") {
      return array(
        'select'=>'t.id',
        'condition'=>'t.create_time < :create_time', 
        'distinct'=>true, 
        'params'=>array(':create_time' => $nextTime));
    }
  }

  private static function getExpiration($nextTime) {
    if ($nextTime < (strtotime(date('y-m-d'))-60*60*24)) {
      $expiration = 0;
    } else {
      $expiration = 60 * 60;
    }
    return $expiration;
  }


  private static function fetchdata(
    &$chart_y, $key, $model, $statement, $category, $expiration) {
      if (Member::isNotInProduction()) {
        $chart_y[$category][] = (int)$model->count($statement);
      } else {
        $mem = new Memcached;
        $mem->addserver('127.0.0.1', 11211);
        $number = $mem->get($key);

        if ($number === false) {
          $cnt = (int)$model->count($statement); 
          $mem->set($key, $cnt, $expiration);
          $chart_y[$category][] = $cnt;
        } else { 
          $chart_y[$category][] = $number;
        }
      }
    }

  private static function isNotInProduction() {
    $host = $_SERVER['HTTP_HOST'];
    if (preg_match('/^dev\./', $host) || preg_match('/^localhost/', $host) ||
      preg_match('/^127\.0\.0\.1/', $host)) {
      return true;
    } else {
      return false;
    }
  }
}