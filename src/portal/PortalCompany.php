<?php

require_once __DIR__ . '/../common/Company.php';

class PortalCompany extends Company {
	public function getPageAdshowsUrl() {
        $imgs = $this->getAdImgUrls();
        $result = array();
        ksort($imgs);
        foreach ($imgs as $img){
            $result[] = $img;
        }
        return $result;
    }
}
