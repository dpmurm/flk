<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "record_list".
 *
 * @property int $number_in_file
 * @property string $cad_obj_num
 * @property string $type_object
 * @property string $status
 * @property string $guid_doc
 * @property int $vid_record_for_export
 * @property string $error_text
 * @property string $error_path_xml
 * @property string $atribut_name
 * @property string $atribut_value
 * @property string $error_type
 * @property int $id
 * @property int $file_name_id
 *
 * @property RecordNotes $recordNotes
 */
class RecordList extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'record_list';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('flk');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number_in_file', 'cad_obj_num', 'type_object', 'status', 'guid_doc', 'vid_record_for_export', 'error_text', 'error_path_xml', 'atribut_name', 'atribut_value', 'error_type'], 'required'],
            [['number_in_file', 'vid_record_for_export', 'file_name_id'], 'integer'],
            [['cad_obj_num', 'type_object', 'status', 'guid_doc', 'atribut_name', 'atribut_value', 'error_type'], 'string', 'max' => 100],
            [['error_text', 'error_path_xml'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'number_in_file' => 'Number In File',
            'cad_obj_num' => 'Кадастровый номер',
            'type_object' => 'Type Object',
            'status' => 'Status',
            'guid_doc' => 'Guid Doc',
            'vid_record_for_export' => 'Vid Record For Export',
            'error_text' => 'Error Text',
            'error_path_xml' => 'Error Path Xml',
            'atribut_name' => 'Atribut Name',
            'atribut_value' => 'Atribut Value',
            'error_type' => 'Error Type',
            'id' => 'ID',
            'file_name_id' => 'File Name ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRecordNotes()
    {
        return $this->hasOne(RecordNotes::className(), ['record_list_id' => 'id']);
    }

    public static function getData($cad_num)
    {
        $data = Yii::$app->flk->createCommand("select 
pe.number, pe.`Year`,pe.`date`,
rl.cad_obj_num, rl.type_object, rl.status,
case 
  WHEN (rl.status!='Прошел флк' and resh.name is null) THEN 'Не обработано'
  WHEN (rl.status='Прошел флк' and resh.name is null) THEN ''
  ELSE resh.name 
END AS resh,
vd.`desc`,
ifnull(rl.error_text,'') as error_text, 
ifnull(rl.error_path_xml,'') as error_path_xml, 
ifnull(rl.atribut_name,'') as atribut_name, 
ifnull(rl.atribut_value,'') as atribut_value, 
ifnull(rl.error_type,'') as error_type
-- , rl.id, rl.file_name_id
from record_list rl
left join record_notes rn on rl.id=rn.record_list_id   
left join protokol_file pf on rl.file_name_id=pf.id 
left join protokol_export pe  on pf.protokol_id=pe.id
left join vid_dok vd on rl.vid_record_for_export=vd.no
left join resheniya resh on resh.id=rn.decision_type
where rl.cad_obj_num='$cad_num'
order by rl.cad_obj_num, pe.`date` desc")->queryAll();
        return $data;
    }
}
