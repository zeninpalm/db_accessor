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

  public function tableName()
  {
    return 'address';
  }

  public function rules()
  {
    return array(
      array('latitude, longitude', 'numerical'),
      array('name', 'required'),
      array('name', 'length', 'max'=>100),
      array('parent_id', 'length', 'max'=>20),
      array('name', 'isRepeatName'),
      array('id, name, parent_id, latitude, longitude', 'safe', 'on'=>'search'),
    );
  }

  public function relations()
  {
    return array(
      'parent' => array(self::BELONGS_TO, 'Address', 'parent_id'),
      'addresses' => array(self::HAS_MANY, 'Address', 'parent_id'),
      'companies' => array(self::HAS_MANY, 'Company', 'address_id'),
      'routers' => array(self::HAS_MANY, 'Router', 'address_id'),
      'support' => array(self::HAS_ONE,'Support','address_id'),
      'users' => array(self::HAS_MANY, 'User', 'address_id'),
    );
  }

  public function attributeLabels()
  {
    return array(
      'id' => 'ID',
      'name' => '名称',
      'parent_id' => '所属地区',
      'latitude' => '纬度',
      'longitude' => '经度',
    );
  }

  public function search()
  {
    $criteria=new CDbCriteria;

    $criteria->compare('id',$this->id,true);
    $criteria->compare('name',$this->name,true);
    $criteria->compare('parent_id',$this->parent_id,true);
    $criteria->compare('latitude',$this->latitude);
    $criteria->compare('longitude',$this->longitude);

    return new CActiveDataProvider($this, array(
      'criteria'=>$criteria,
    ));
  }

  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }

  public static function getAddressId($province,$city,$area){
    if($province==null||strlen($province)<1){
      return 1;
    }

    $_province = Address::findAddress(1, $province);
    if($city==null||strlen($city)<1){
      return $_province->id;
    }
    $_city = Address::findAddress($_province->id, $city);

    if($area==null||strlen($area)<1){
      return $_city->id;
    }
    $_area = Address::findAddress($_city->id, $area);

    return $_area->id;
  }

  public static function findAddress($parentId, $name) {
    $item = Address::model()->findByAttributes(array('parent_id'=>$parentId,'name'=>$name));
    if($item==null){
      $item = new Address;
      $item->name = $name;
      $item->parent_id = $parentId;
      $item->save();
    }
    return $item;
  }

  public function getFullName($name=''){
    if($this->parent!=null && $this->parent->id!=1){
      return $this->parent->getFullName($this->name.$name);
    }
    return $this->name.$name;
  }

  public function getSupport(){
    $result = array();
    $tmpAddr = $this;
    while ($tmpAddr!=NULL){
      if($tmpAddr->support !=NULL){
        $result['id'.$tmpAddr->support->id] = $tmpAddr->support;
      }
      $tmpAddr = $tmpAddr->parent;
    }
    return $result;
  }

  public function reStoreParent() {
    if($this->parent_id==1){

    }else if($this->parent->parent_id==1){
      $this->province = $this->parent_id;
    }else{
      $this->province = $this->parent->parent_id;
      $this->city = $this->parent_id;
    }
  }

  public function storeParentAddress() {
    if($this->city!=''||$this->city>0){
      $this->parent_id = $this->city;
    }else if($this->province!=''&&$this->province>0){
      $this->parent_id = $this->province;
    }else{
      $this->parent_id = 1;
    }
  }

  public function getIdsWithSub(){
    $results = array($this->id);
    foreach ($this->addresses as $tmpAddr) {
      $results = array_merge($results,$tmpAddr->getIdsWithSub());
    }
    return $results;
  }

}

