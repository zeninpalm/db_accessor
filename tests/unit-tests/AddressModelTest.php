<?php
require_once 'bootstrap.php';
require_once __DIR__ . '/../../src/Address.php';


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
}

