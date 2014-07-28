<?php

/**
 * This is the model class for table "node".
 *
 * The followings are the available columns in table 'node':
 * @property string $id
 * @property string $policy_id
 * @property string $name
 * @property string $gw_id
 * @property integer $last_heartbeat_at
 * @property string $last_heartbeat_ip
 * @property string $last_heartbeat_sys_uptime
 * @property string $last_heartbeat_sys_memfree
 * @property double $last_heartbeat_sys_load
 * @property string $last_heartbeat_wifidog_uptime
 * @property integer $deployment_status
 * @property integer $update_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Connection[] $connections
 * @property Policy $policy
 */
class Node extends CActiveRecord
{
    
    
        public static $STATUS_DEPLOY = 1;
        public static $STATUS_STOP = 0;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'node';
	}

        public function beforeSave(){
            if(parent::beforeSave()){
                if($this->isNewRecord){
                    $this->create_time = time();
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
			array('policy_id, name, gw_id', 'required'),
			array('deployment_status, update_time,create_time, last_heartbeat_at', 'numerical', 'integerOnly'=>true),
			array('last_heartbeat_sys_load', 'numerical'),
			array('policy_id, last_heartbeat_sys_uptime, last_heartbeat_sys_memfree, last_heartbeat_wifidog_uptime', 'length', 'max'=>20),
			array('name', 'length', 'max'=>150),
			array('gw_id', 'length', 'max'=>50),
			array('last_heartbeat_ip', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, policy_id, name, gw_id, last_heartbeat_at, last_heartbeat_ip, last_heartbeat_sys_uptime, last_heartbeat_sys_memfree, last_heartbeat_sys_load, last_heartbeat_wifidog_uptime, deployment_status, update_time, create_time', 'safe', 'on'=>'search'),
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
			'connections' => array(self::HAS_MANY, 'Connection', 'node_id'),
			'policy' => array(self::BELONGS_TO, 'Policy', 'policy_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'policy_id' => 'Policy',
			'name' => 'Name',
			'gw_id' => 'Gw',
			'last_heartbeat_at' => 'Last Heartbeat At',
			'last_heartbeat_ip' => 'Last Heartbeat Ip',
			'last_heartbeat_sys_uptime' => 'Last Heartbeat Sys Uptime',
			'last_heartbeat_sys_memfree' => 'Last Heartbeat Sys Memfree',
			'last_heartbeat_sys_load' => 'Last Heartbeat Sys Load',
			'last_heartbeat_wifidog_uptime' => 'Last Heartbeat Wifidog Uptime',
			'deployment_status' => '0- disable
1 - valid
',
			'update_time' => 'Update Time',
			'create_time' => 'Create Time',
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
		$criteria->compare('policy_id',$this->policy_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('gw_id',$this->gw_id,true);
		$criteria->compare('last_heartbeat_at',$this->last_heartbeat_at,true);
		$criteria->compare('last_heartbeat_ip',$this->last_heartbeat_ip,true);
		$criteria->compare('last_heartbeat_sys_uptime',$this->last_heartbeat_sys_uptime,true);
		$criteria->compare('last_heartbeat_sys_memfree',$this->last_heartbeat_sys_memfree,true);
		$criteria->compare('last_heartbeat_sys_load',$this->last_heartbeat_sys_load);
		$criteria->compare('last_heartbeat_wifidog_uptime',$this->last_heartbeat_wifidog_uptime,true);
		$criteria->compare('deployment_status',$this->deployment_status);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Node the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function markExpiredConnection(){
            $time =time();
            $expiredMsg = 'Expired at: '.  date('Y-m-d H:i:s', $time);
            Yii::app()->db->createCommand("UPDATE connection SET status = ".Connection::$EXPIRED." , disconnect_reason = '".$expiredMsg."' , update_time = ".time()."  WHERE node_id=:node_id AND disconnect_time<=:disconnect_time AND status=:status")->bindValues(array(':node_id' => $this->id, ':disconnect_time' => $time,':status'=>  Connection::$TOKEN_VALIDATED))->execute();
        }

        public function getNumActiveConnections() {
            return Connection::model()->countByAttributes(array('node_id'=>$this->id,'status'=>Connection::$TOKEN_VALIDATED));
        }

}
