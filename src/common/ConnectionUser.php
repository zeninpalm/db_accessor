<?php

/**
 * This is the model class for table "connection_user".
 *
 * The followings are the available columns in table 'connection_user':
 * @property integer $id
 * @property string $mobile
 * @property string $mac
 * @property string $node
 * @property integer $sms_verified
 * @property integer $router_id
 * @property string $url
 * @property string $veri_code
 * @property integer $status
 * @property integer $add_time
 * @property integer $update_time
 * @property integer $company_id
 *
 * @property Company $company
 * @property Router $router
 */
class ConnectionUser extends CActiveRecord
{
    public $mac;
    public $address;
    public $port;
    /**
     * for stat
     */
    public $statCount;
    public $statTime;

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->add_time = time();
            }
            return true;
        }
        return false;
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'connection_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('mobile, mac, node', 'required'),
            array('company_id, status, add_time, update_time, sms_verified', 'numerical', 'integerOnly' => true),
            array('mobile', 'length', 'max' => 13),
            array('mac, node, veri_code', 'length', 'max' => 45),
            array('url', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, mobile, mac, node, company_id, url, veri_code, status, add_time, update_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.

        return array(
            'company' => array(self::BELONGS_TO, 'Company', 'company_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => '主键id',
            'mobile' => '手机号码',
            'mac' => 'MAC地址',
            'node' => '路由器网关id',
            'company_id' => '店铺id',
            'sms_verified' => '是否验证',
            'url' => '客户端初始URL',
            'veri_code' => '验证码',
            'status' => '状态 0:未验证，1：已验证；2：已过期',
            'add_time' => '添加时间',
            'update_time' => '更新时间',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ConnectionUser the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function isValidAccess()
    {
        if ($this->node == null || $this->mac == NULL || $this->address == null || $this->port == null) {
            return false;
        }
        return true;
    }

    public function getAuthPuppyQuery()
    {
        return http_build_query(array('gw_address' => $this->address, 'gw_port' => $this->port, 'gw_id' => $this->node, 'mac' => $this->mac));
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
