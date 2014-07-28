<?php

/**
 * This is the model class for table "member".
 *
 * The followings are the available columns in table 'member':
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $nick_name
 * @property string $mobile
 * @property string $header
 * @property string $header_id
 * @property string $signature
 * @property integer $create_time
 * @property integer $dob
 * @property integer $gender
 *
 * The followings are the available model relations:
 * @property CompanyMember[] $companyMembers
 * @property MemberDevice[] $memberDevices
 */
class Member extends CActiveRecord
{
    /**
     * for stat
     */
    public $statCount;
    public $statTime;
        
  /**
   * @return string the associated database table name
   */
  public function tableName()
  {
    return 'member';
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
      array('create_time, dob, gender', 'numerical', 'integerOnly'=>true),
      array('name', 'length', 'max'=>40),
      array('email, password, nick_name', 'length', 'max'=>100),
      array('mobile', 'length', 'max'=>20),
      array('header, signature', 'length', 'max'=>500),
      // The following rule is used by search().
      // @todo Please remove those attributes that should not be searched.
      array('id, name, email,gender, password, nick_name, mobile, header, signature, create_time', 'safe', 'on'=>'search'),
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
      'companyMembers' => array(self::HAS_MANY, 'CompanyMember', 'member_id'),
      'memberDevices' => array(self::HAS_MANY, 'MemberDevice', 'member_id'),
    );
  }
        
        /**
   * @return array customized attribute labels (name=>label)
   */
  public function attributeLabels()
  {
    return array(
      'id' => 'ID',
      'name' => '姓名',
      'email' => '邮件',
      'password' => '密码',
      'nick_name' => '昵称',
      'mobile' => '主要手机号',
      'header' => '头像',
                        'dob' => '出生年月',
      'signature' => '签名',
                        'gender' => '性别',
      'create_time' => '创建时间',
    );
  }


  /**
   * Returns the static model of the specified AR class.
   * Please note that you should have this exact method in all your CActiveRecord descendants!
   * @param string $className active record class name.
   * @return Member the static model class
   */
  public static function model($className=__CLASS__)
  {
    return parent::model($className);
  }

        public static function createMember($connectionUser) {
                if($connectionUser->sms_verified && $connectionUser->mobile!=null){
                    $member = Member::model()->findByAttributes(array('mobile'=>$connectionUser->mobile));
                    if($member==null){
                        $member = new Member;
                        $member->nick_name = $connectionUser->mobile;
                        $member->mobile = $connectionUser->mobile;
                        $member->password = md5(rand(100000, 999999));
                        $member->save();
                    }
                    MemberDevice::saveDevice($member,$connectionUser);
                }
        }

}
