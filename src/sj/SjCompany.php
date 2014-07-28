<?php

require_once __DIR__ . '/../common/Company.php';

class SjCompany extends Company {
	public function defaultScope()
    {
        if (Yii::app()->user->getIsShopper()) {
            return array(
                'condition' => "user_id = :userId",
                'params' => array(':userId' => Yii::app()->user->getId()),
            );
        } else if (Yii::app()->user->getIsAdmin()) {
            $criteria = new CDbCriteria;
            $criteria->addInCondition('address_id', Yii::app()->user->getAddressIds());
            return $criteria;
        }
        return array();
    }

    public function rules() {
    	$rules = parent::rules();
    	$additionals = array(
    		array('node', 'isRepeatNode'),
    		array('address_id', 'isRightAddress'),
    	);
    	return array_merge($rules, $additionals);
    }

    public function isRightAddress()
    {
        if (count($this->getErrors('address_id')) == 0 && Yii::app()->user->getIsAdmin()) {
            $addr = Address::model()->findByPk($this->address_id);
            $result = false;
            $userAddrId = Yii::app()->user->getUser()->address_id;
            while ($addr != null && !$result) {
                $result = ($addr->id == $userAddrId);
                $addr = $addr->parent;
            }
            if (!$result) {
                $this->addError('address_id', '你只能在区域【' . Yii::app()->user->getUser()->address->name . '】添加商家.');
            }
        }
    }

    public function attributeLabels() {
    	$labels = parent::attributeLabels();
    	$additionals = array(
    		'category_parent' => '商家类别【父类】',
    	);
    	return array_merge($labels, $additionals);
    }
        public function getNodes()
    {
        $result = array();
        foreach ($this->routers as $key) {
            $result[] = $key->node;
        }
        return $result;
    }

    public function isRepeatNode()
    {
        if ($this->node != null && strlen($this->node) > 0) {
            $this->isRepeat('node', $this->node, '网关ID');
        }
    }

    private function isRepeat($attr, $value, $msg)
    {
        if (count($this->getErrors($attr)) == 0) {
            $item = Company::model()->findByAttributes(array($attr => $value));
            if ($item != null && $item->id != $this->id) {
                $this->addError($attr, $msg . '重复.');
            }
        }
    }

    public function getCommercialStatuse()
    {
        switch ($this->commercial) {
            case 0:
                return '未启用';
            case 1:
                return '正常';
            case 2:
                return '取消合作';
        }
    }

    public function getCommercialSwitchStatuse()
    {
        switch ($this->commercial) {
            case 0:
                return '开启';
            case 1:
                return '取消合作';
            case 2:
                return '开启';
        }
    }

    public function getCommercialSatuses()
    {
        return array('未安装', '已安装', '取消合作');
    }

    public function getAddresInfo()
    {
        return $this->address->getFullNameInRouter();
    }

    public function getImg()
    {
        $img = CompanyImg::model()->findBySql('select img from company_img where company_id=' . $this->id . ' order by id desc');
        return $img['img'];
    }

    public function getRouterStatus()
    {
        $output = array();
        $result = Yii::app()->puppy->nodeStatus($this->node . ',');
        if ($result['result'] == 'success') {
            $result = $result['data'];
            if (isset($result[$this->node])) {
                $item = $result[$this->node];
                $output[$this->node] = array('result' => $this->countRouterStatus($item)
                );
            } else {
                $output[$this->node] = array('result' => '获取失败');
            }
        } else {
            $output[$this->node] = array('result' => '获取失败');
        }
        return $output[$this->node]['result'];
    }

    private function countRouterStatus($item)
    {
        if ($item['lastHeart'] == null || (time() - $item['lastHeart']) > 86400) {
            return '失效24小时';
        } else if ((time() - $item['lastHeart']) > 900) {
            return '暂时失效';
        }
        return '正常';
    }
}