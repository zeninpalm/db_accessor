<?php
require_once 'bootstrap.php';
require_once __DIR__ . '/../../src/common/Address.php';


class AddressModelTest extends PHPUnit_Framework_TestCase
{
  private $address;

  public function setUp() {
    $this->address = new Address;
  }

  public function testTableName() {
    $this->assertEquals(
      'address',
      $this->address->tableName());
  }

  public function testRules() {
    $expected_rules = array(
			array('latitude, longitude', 'numerical'),
                        array('name', 'required'),
			array('name', 'length', 'max'=>100),
			array('parent_id', 'length', 'max'=>20),
                        array('name', 'isRepeatName'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, parent_id, latitude, longitude', 'safe', 'on'=>'search'),
		);

    $this->assertEquals(
      $expected_rules,
      $this->address->rules()
    );
  }

  public function testRelations() {
    $expected_relations = array(
			'parent' => array(CActiveRecord::BELONGS_TO, 'Address', 'parent_id'),
			'addresses' => array(CActiveRecord::HAS_MANY, 'Address', 'parent_id'),
			'companies' => array(CActiveRecord::HAS_MANY, 'Company', 'address_id'),
			'routers' => array(CActiveRecord::HAS_MANY, 'Router', 'address_id'),
                        'support' => array(CActiveRecord::HAS_ONE,'Support','address_id'),
                        'users' => array(CActiveRecord::HAS_MANY, 'User', 'address_id'),
		);
    $this->assertEquals(
      $expected_relations,
      $this->address->relations()
    );
  }

  public function testAttributeLabels() {
    $expected_labels = array(
			'id' => 'ID',
			'name' => '名称',
			'parent_id' => '所属地区',
			'latitude' => '纬度',
			'longitude' => '经度',
		);
    $this->assertEquals(
      $expected_labels,
      $this->address->attributeLabels()
    );
  }

  public function testSearch() {
    $dataProvider = $this->makeDataProvider();
    $this->assertEquals($dataProvider, $this->address->search());
  }

  public function testModel() {
    $model = CActiveRecord::model('Address');
    $this->assertEquals($model, $this->address->model());
  }

  public function testGetAddressIdWitNullOrEmptyProvince() {
    $this->assertEquals(1, Address::getAddressId(null, null, null));
  }

  public function testGetAddressIdWithProvinceAndNullCity() {
    $province = $this->findAddressWithProvince();
    $this->assertEquals(
      $province->id,
      Address::getAddressId("SiChuan", null, null)
    );
  }

  public function testGetAddressIdWithProvinceCityAndNullArea() {
    $city = $this->findAddressWithProvinceCity();
    $this->assertEquals(
      $city->id,
      Address::getAddressId("SiChuan", "Chengdu", null)
    );
  }

  public function testGetAddressIdWithProvinceCityAndArea() {
    $area = $this->findAddressWithCityArea();
    $this->assertEquals(
      $area->id,
      Address::getAddressId("SiChuan", "Chengdu", "Wuhou")
    );
  }

  public function testFindAddress() {
    $item = Address::model()->findByAttributes(
      array(
        'parentId'=>1,
        'name'=>"SiChuan"
      )
    );
    $this->assertEquals(
      $item,
      Address::findAddress(1, "SiChuan")
    );
  }

  //TODO:
  // Add tests for getFullName, getSupport, 
  // reStoreParent, storeParentAddress and getIdsWithSub
  //

  private function makeDataProvider() {
    $criteria = new CDbCriteria;
    $criteria->compare('id', $this->address->id, true);
    $criteria->compare('name', $this->address->name, true);
    $criteria->compare('parent_id', $this->address->parent_id, true);
    $criteria->compare('latitude', $this->address->latitude);
    $criteria->compare('longitude', $this->address->longitude);

    return new CActiveDataProvider($this->address, array(
      'criteria' => $criteria,
    ));
  }

  private function findAddressWithProvince() {
    return Address::findAddress(1, "Sichuan");
  }

  private function findAddressWithProvinceCity() {
    return Address::findAddress("Sichuan", "Chengdu");
  }

  private function findAddressWithCityArea() {
    return Address::findAddress("Chengdu", "Wuhou");
  }
}

