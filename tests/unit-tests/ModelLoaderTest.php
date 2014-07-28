<?php

require_once __DIR__ . '/../../src/ModelLoader.php';

class MyModel {
	public static $TYPE = "MyModelType";

	public static function model() {
		return new MyModel();
	}

	public function test() {
		return true;
	}
}

class SuperMyModel {
	public static function model() {
		return new SuperMyModel();
	}

	public function test() {
		return 18981930480;
	}
}


class ModelLoaderTest extends PHPUnit_Framework_TestCase {
	public function testLoad() {
		$obj = ModelLoader::load('', 'MyModel');
		$obj2 = ModelLoader::load('Super', 'MyModel');

		$this->assertTrue($obj::model()->test());
		$this->assertEquals(
			"MyModelType",
			$obj::$TYPE);
		$this->assertEquals(
			18981930480,
			$obj2::model()->test());
	}
}