<?

require_once __DIR__ . '/../common/User.php';

class WifiAuthUser extends User {
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

	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, name, password', 'required'),
			array('is_active, update_time, create_time', 'numerical', 'integerOnly'=>true),
			array('email', 'length', 'max'=>255),
			array('name, password', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, email, name, password, is_active, update_time, create_time', 'safe', 'on'=>'search'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'email' => 'Email',
			'name' => 'Name',
			'password' => 'Password',
			'is_active' => 'Is Active',
			'update_time' => 'Update Time',
			'create_time' => 'Create Time',
		);
	}

	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}