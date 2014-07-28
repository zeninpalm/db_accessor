<?php

/**
 * This is the model class for table "connection".
 *
 * The followings are the available columns in table 'connection':
 * @property string $id
 * @property string $node_id
 * @property string $token
 * @property integer $status
 * @property string $mac
 * @property string $ip
 * @property string $auth_type
 * @property string $identity
 * @property double $incoming
 * @property double $outgoing
 * @property string $user_agent
 * @property string $disconnect_reason
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $disconnect_time
 *
 * The followings are the available model relations:
 * @property Node $node
 */
class Connection extends CActiveRecord
{
    
        public static $WAITING_TOKEN_VALIDATION = 0;
        public static $TOKEN_VALIDATED = 1;
        public static $LOGGED_OUT = 2;
        public static $EXPIRED = 3;
        
        public static $STATUS_MAP = array(0=>'Waiting Verfy',1=>'Verfied',2=>'Logout',3=>'Expired');


        /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'connection';
	}

        public function setToken(){
            $this->token = sha1(rand().time());
        }

        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->incoming = 0;
                    $this->outgoing = 0;
                    $this->status = self::$WAITING_TOKEN_VALIDATION;
                    $this->create_time = time();
                    $this->update_time = time();
                }else{
                    $this->update_time = time();
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
			array('node_id, token', 'required'),
			array('status, create_time, update_time, disconnect_time', 'numerical', 'integerOnly'=>true),
			array('incoming, outgoing', 'numerical'),
			array('node_id', 'length', 'max'=>20),
			array('token, mac, ip, auth_type, identity', 'length', 'max'=>255),
			array('user_agent, disconnect_reason', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, node_id, token, status, mac, ip, auth_type, identity, incoming, outgoing, user_agent, disconnect_reason, create_time, update_time, disconnect_time', 'safe', 'on'=>'search'),
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
			'node' => array(self::BELONGS_TO, 'Node', 'node_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'node_id' => 'Node',
			'token' => 'Token',
			'status' => '0 - not connected
1 - connected',
			'mac' => 'Mac',
			'ip' => 'Ip',
			'auth_type' => 'Auth Type',
			'identity' => 'Identity',
			'incoming' => 'Incoming',
			'outgoing' => 'Outgoing',
			'user_agent' => 'User Agent',
			'disconnect_reason' => 'Disconnect Reason',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'disconnect_time' => 'Disconnect Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('node_id',$this->node_id,true);
		$criteria->compare('token',$this->token,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('mac',$this->mac,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('auth_type',$this->auth_type,true);
		$criteria->compare('identity',$this->identity,true);
		$criteria->compare('incoming',$this->incoming);
		$criteria->compare('outgoing',$this->outgoing);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('disconnect_reason',$this->disconnect_reason,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('disconnect_time',$this->disconnect_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Connection the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function statusText(){
            return self::$STATUS_MAP[$this->status];
        }
}
