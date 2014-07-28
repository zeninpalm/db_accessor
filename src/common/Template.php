<?php

/**
 * This is the model class for table "template".
 *
 * The followings are the available columns in table 'template':
 * @property string $id
 * @property string $name
 * @property string $notes
 * @property string $script
 * @property integer $update_time
 * @property integer $create_time
 *
 * The followings are the available model relations:
 * @property Advertisement[] $advertisements
 */
class Template extends CActiveRecord
{

    private $params = '';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'template';
    }

    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
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
            array('update_time, create_time', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 40),
            array('notes, script', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, notes, script, update_time, create_time', 'safe', 'on' => 'search'),
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
            'advertisements' => array(self::HAS_MANY, 'Advertisement', 'template_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => '名称',
            'notes' => '注释',
            'script' => '脚本',
            'update_time' => '更新日期',
            'create_time' => '创建日期',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('notes', $this->notes, true);
        $criteria->compare('script', $this->script, true);
        $criteria->compare('update_time', $this->update_time);
        $criteria->compare('create_time', $this->create_time);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Template the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getParameters(){
        if($this->params==''){
            $flag = preg_match_all('/@[^:@]{1,}:[^:@]{1,}:[^:@]{1,}@/',$this->script,$temp);
            if($flag>0){
                $array = array();
                foreach($temp[0] as $tmp){
                    $array[] = str_replace('@','',$tmp);
                }
                $this->params = CJSON::encode($array);
            }
        }
        return $this->params;
    }
}
