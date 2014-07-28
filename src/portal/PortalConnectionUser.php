<?php

require_once __DIR__ . '/../common/ConnectionUser.php';

class PortalConnectionUser extends ConnectionUser {
    public static function parseGateWay($address, $port, $identity, $url, $mac)
    {
        $tmp = new ConnectionUser;
        $tmp->url = $url;
        $tmp->node = $identity;
        $tmp->company = Company::model()->findByAttributes(array('node' => $identity));
        $tmp->company_id = $tmp->company != null ? $tmp->company->id : 0;
        $tmp->sms_verified = 1;
        $tmp->mac = $mac;
        $tmp->port = $port;
        $tmp->address = $address;
        return $tmp;
    }

    public function isStopSMS()
    {
        if ($this->company != null && $this->company->stop_sms) {
            return true;
        }
        return false;
    }

    public function isStopAuth()
    {
        if ($this->company != null && $this->company->stop_auth) {
            return true;
        }
        return false;
    }

    public static function findVeriCode($mobile, $mac)
    {
        $result = rand(1000, 9999);
        $criteria = new CDbCriteria;
        $criteria->addCondition('veri_code is not null');
        $criteria->addCondition('add_time >' . (time() - 600));
        $criteria->addCondition('mobile =:mobile');
        $criteria->params[':mobile'] = $mobile;
        $criteria->addCondition('mac =:mac');
        $criteria->params[':mac'] = $mac;
        $criteria->order = 'id DESC';
        $criteria->limit = 1;
        $conns = ConnectionUser::model()->findAll($criteria);
        if (count($conns) > 0) {
            $conn = $conns[0];
            if (strlen($conn->veri_code) > 3) {
                $result = $conn->veri_code;
            }
        }
        return $result;
    }
}