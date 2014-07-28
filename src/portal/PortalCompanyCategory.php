<?php

require_once __DIR__ . '/../common/CompanyCategory.php';

class PortalCompanyCategory extends CompanyCategory {
    public function getFullCategory($name = '')
    {
        if($this->parentCategory!=null && $this->parent_id > 0){
            return $this->parentCategory->getFullCategory($name.($name==''?'':',').$this->name);
        }
        return $name.','.$this->name;

    }
}
