<?php

require_once __DIR__ . '/../../src/ModelLoader.php';
require_once __DIR__ . '/../../src/portal/PortalAddress.php';


class PortalAddressModelTest extends PHPUnit_Framework_TestCase {
	public function testLoadPortalAddress() {
		$cls= ModelLoader::load('Portal', "Address");
		$cls::model();
	}
}