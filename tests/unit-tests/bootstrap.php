<?php

class DummyObject {
  public $id =  18981930480;
  public function findByAttributes($arr) {
    return new DummyObject;
  }
}

class CActiveRecord {
  const BELONGS_TO = "belongs_to";
  const HAS_MANY = "has_many";
  const HAS_ONE = "has_one";
  public $id;
  public $name;
  public $parent_id;
  public $latitude;
  public $longitude;
  public $parent;

  public function __construct() {
    $this->name = "A Given Name";
  }

  public static function model($className=__CLASS__) {
    return new DummyObject;
  }
}

class CDbCriteria {
  public function compare($name, $col, $flag=false) {
    return null;
  }
}

class CActiveDataProvider {
  public function __construct($object, $options) {
  }
}


