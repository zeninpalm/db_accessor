<?php

/**
 * This is the model class for table "member_device".
 *
 * The followings are the available columns in table 'member_device':
 * @property string $id
 * @property string $member_id
 * @property string $mobile
 * @property string $mac
 * @property string $brand
 * @property string $model
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Member $member
 */
class MemberDevice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'member_device';
	}

        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->create_time = time();
                }
                return true;                
            }
            return false;
        }
        
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'numerical', 'integerOnly'=>true),
			array('member_id, mobile', 'length', 'max'=>20),
			array('mac, brand, model', 'length', 'max'=>40),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, member_id, mobile, mac, brand, model, create_time', 'safe', 'on'=>'search'),
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
			'member' => array(self::BELONGS_TO, 'Member', 'member_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'member_id' => '所属用户',
			'mobile' => '手机号',
			'mac' => 'MAC地址',
			'brand' => '设备品牌',
			'model' => 'Model',
			'create_time' => 'Create Time',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MemberDevice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public static function findHistory($mac) {
            $result = MemberDevice::model()->findByAttributes(array('mac'=>$mac));
            return $result==null? NULL:$result->mobile;
        }

        public static function saveDevice($member, $connectionUser) {
            $find = MemberDevice::model()->findByAttributes(array('member_id'=>$member->id,'mac'=>$connectionUser->mac));
            if($find==null){
                $find = new MemberDevice;
                $find->member_id = $member->id;
                $find->mobile = $connectionUser->mobile;
                $find->mac = $connectionUser->mac;
                $find->save();
            }
            if($connectionUser->company!=null && $connectionUser->company_id>0){
                $cmember = CompanyMember::findMember($connectionUser->company_id,$member->id);
                if($cmember==null){
                    $cmember = new CompanyMember;
                    $cmember->company_id = $connectionUser->company_id;
                    $cmember->member_id = $member->id;
                    $cmember->mobile = $connectionUser->mobile;
                }else{
                    $cmember->login_time = $cmember->login_time + 1;
                }
                $cmember->save();
            }
        }

}
