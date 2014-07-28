<?php

/**
 * This is the model class for table "advertisement".
 *
 * The followings are the available columns in table 'advertisement':
 * @property string $id
 * @property string $name
 * @property string $position_id
 * @property string $user_id
 * @property string $template_id
 * @property string $template_val
 * @property integer $is_valid
 * @property string $keywords
 * @property string $day_view_time
 * @property string $day_max_time
 * @property string $view_time
 * @property string $max_time
 * @property string $click_time
 * @property double $price
 * @property string $script
 * @property integer $start_time
 * @property integer $end_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Template $template
 * @property Position $position
 * @property Resource[] $resources
 * @property ViewHistory[] $viewHistories
 */
class Advertisement extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'advertisement';
	}

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->create_time = time();
            }
            return true;
        }
        return false;
    }

    public function afterSave(){
        if ($this->isNewRecord) {
            $this->transTemplate();
            $this->setIsNewRecord(false);
            $this->template_val = CJSON::encode($this->template_val_array);
            $this->save();
        }
    }

    public function beforeDelete(){
        foreach($this->resources as $res){
            Yii::app()->asset->deleteFile($res->source_id);
            $res->delete();
        }
        parent::beforeDelete();
        return true;
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('name, position_id, template_id, is_valid, start_time, end_time, day_max_time, max_time', 'required'),
			array('is_valid, start_time, end_time, create_time', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('name, user_id, position_id, template_id, day_view_time, day_max_time, view_time, max_time, click_time', 'length', 'max'=>20),
			array('keywords', 'length', 'max'=>400),
			array('template_val, script', 'safe'),
            array('template_id','isValidTemplate'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, user_id, position_id, template_id, template_val, is_valid, keywords, day_view_time, day_max_time, view_time, max_time, click_time, price, script, start_time, end_time, create_time', 'safe', 'on'=>'search'),
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
			'template' => array(self::BELONGS_TO, 'Template', 'template_id'),
            'position' => array(self::BELONGS_TO, 'Position', 'position_id'),
			'resources' => array(self::HAS_MANY, 'Resource', 'advertisement_id'),
			'viewHistories' => array(self::HAS_MANY, 'ViewHistory', 'advertisement_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '广告名称',
			'user_id' => '创建用户',
            'position_id' => '位置',
			'template_id' => '模板',
			'template_val' => '模板变量值',
			'is_valid' => '是否有效',
			'keywords' => '关键字',
			'day_view_time' => '每日显示次数',
			'day_max_time' => '每日最大显示次数',
			'view_time' => '浏览次数',
			'max_time' => '总共最大显示次数',
			'click_time' => '点击次数',
			'price' => '价格',
			'script' => '脚本',
			'start_time' => '开始时间',
			'end_time' => '结束时间',
			'create_time' => '创建时间',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('user_id',$this->user_id,true);
        $criteria->compare('position_id', $this->position_id, true);
		$criteria->compare('template_id',$this->template_id,true);
		$criteria->compare('template_val',$this->template_val,true);
		$criteria->compare('is_valid',$this->is_valid);
		$criteria->compare('keywords',$this->keywords,true);
		$criteria->compare('day_view_time',$this->day_view_time,true);
		$criteria->compare('day_max_time',$this->day_max_time,true);
		$criteria->compare('view_time',$this->view_time,true);
		$criteria->compare('max_time',$this->max_time,true);
		$criteria->compare('click_time',$this->click_time,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('script',$this->script,true);
		$criteria->compare('start_time',$this->start_time);
		$criteria->compare('end_time',$this->end_time);
		$criteria->compare('create_time',$this->create_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Advertisement the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function getStatus(){
        return array(0=>'无效',1=>'有效');
    }

    public function transTime(){
        if($this->start_time!=null && strlen($this->start_time)>0){
            $this->start_time = strtotime($this->start_time);
        }
        if($this->end_time!=null && strlen($this->end_time)>0){
            $this->end_time = strtotime($this->end_time);
        }
    }

    public function revertTime(){
        if($this->start_time>0){
            $this->start_time = date('Y-m-d\TH:i',$this->start_time);
        }
        if($this->end_time>0){
            $this->end_time = date('Y-m-d\TH:i',$this->end_time);
        }
    }

    public function isValidTemplate(){
        if($this->template_id>0){
            $this->template = Template::model()->findByPk($this->template_id);
            if($this->template!=null){
                return $this->verifyParameters();
            }
        }
        $this->addError('template_id','错误的模板文件');
    }

    public  function verifyParameters(){
        $params = CJSON::decode($this->template->getParameters());
        if($params==null){
            return true;
        }
        foreach($params as $param){
            $field = explode(':', $param);
            if (count($field) == 3) {
                switch ($field[1]) {
                    case 'text':
                        if(!isset($_POST[$field[0]]) || trim($_POST[$field[0]])==''){
                            $this->addError('template_id','请完整填写模板要求提供的内容');
                            return false;
                        }
                        break;
                    case 'file':
                        $file = CUploadedFile::getInstanceByName($field[0]);
                        if($file==null){
                            $this->addError('template_id','请完整填写模板要求提供的内容');
                            return false;
                        }
                        break;
                }
            }
        }
        return true;
    }

    public function transTemplate(){
        $params = CJSON::decode($this->template->getParameters());
        $this->template_val_array = array();
        $this->script = $this->template->script;
        if($params==null){
            return true;
        }
        foreach($params as $param){
            $this->script = str_replace('@'.$param.'@', $this->handleParamField($param),$this->script);
        }
    }

    public function updateTemplate(){
        $this->template_val_array = CJSON::decode($this->template_val);
        $this->script = $this->template->script;
        $params = CJSON::decode($this->template->getParameters());
        foreach($params as $param){
            $field = explode(':', $param);
            $this->script = str_replace('@'.$param.'@', $this->template_val_array[$field['0']],$this->script);
        }
    }

    private $template_val_array = array();

    public function handleParamField($param)
    {
        $field = explode(':', $param);
        if (count($field) == 3) {
            switch ($field[1]) {
                case 'text':
                    $this->template_val_array = array_merge($this->template_val_array,array($field[0]=>$_POST[$field[0]]));
                    return $_POST[$field[0]];
                case 'file':
                    return $this->saveResource($field);
            }
        }
    }

    /**
     * @param $field
     * @return mixed
     */
    public function saveResource($field)
    {
        $resource = new Resource();
        $resource->advertisement_id = $this->id;
        $resource->key = $field[0];
        $tmpFile = CUploadedFile::getInstanceByName($field[0]);
        $file = Yii::app()->asset->saveFile(0, $tmpFile, 'advertisement:' . $this->id);
        if ($file['result'] == 'success') {
            $resource->source_id = $file['data']['id'];
            $resource->source_path = $file['data']['url'];
            $this->template_val_array = array_merge($this->template_val_array, array($field[0] => $resource->source_path));
        }
        $resource->save();
        return $resource->source_path;
    }
}
