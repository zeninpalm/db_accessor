<?php

require_once __DIR__ . '/../common/CompanyCategory.php';

class SjCompanyCategory extends CompanyCategory {
    public function getChildrenIds(){
        $results = array($this->id);
        foreach ($this->children as $tmpAddr) {
            $results = array_merge($results,$tmpAddr->getChildrenIds());
        }
        return $results;
    }
}