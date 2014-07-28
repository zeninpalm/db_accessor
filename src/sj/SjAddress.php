<?php

require_once __DIR__ . '/../common/Address.php';

class SjAddress extends Address {
  public function getFullNameInRouter($name = '') {
    if ($this->parent != null && $this->parent->id != 1) {
      if ($name != '') {
        return $this->parent->getFullNameInRouter($this->name . '-' . $name);
      } else {
        return $this->parent->getFullNameInRouter($this->name);
      }
    }

    return $this->name . '-' . $name;
  }
}
