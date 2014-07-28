<?php

require_once __DIR__ . '/../../src/ModelLoader.php';
require_once __DIR__ . '/../../src/sj/SjAddress.php';


class SjAddressModelTest extends PHPUnit_Framework_TestCase {
	public function testLoadPortalAddress() {
		$cls= ModelLoader::load('Sj', "Address");
		$cls::model();
	}
}