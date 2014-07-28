<?php

require_once __DIR__ . '/../common/Address.php';

class PortalAddress extends Address {
	public function getFullPath($name = '')
	{
	  	if ($this->parent != null && $this->parent->id != 1) {
	      	return $this->parent->getFullPath($name . ($name == '' ? '' : ',') . $this->name);
	  	}
	  	return $name . ',' . $this->name;
	}
}