<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  F-RevoCRM Open Source
 * The Initial Developer of the Original Code is F-RevoCRM.
 * Portions created by thinkingreed are Copyright (C) F-RevoCRM.
 * All Rights Reserved.
 ************************************************************************************/

class Settings_ModuleBuilder_Record_Model extends Settings_Vtiger_Record_Model {

    /**
     * Function to get Id of this record instance
     * @return <Integer> id
     */
    public function getId() {
        return $this->get('id');
    }

    /*
     * Function to get Name of this record
     * @return <String>
     */
    public function getName() {
        return $this->get('name');
    }

    /**
     * Function to get module instance of this record
     * @return <type>
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * Function to set module to this record instance
     * @param <Settings_ModuleBuilder_Record_Model> $moduleModel
     * @return <Settings_ModuleBuilder_Record_Model> record model
     */
    public function setModule($moduleModel) {
        $this->module = $moduleModel;
        return $this;
    }

    /**
     * Function to get display value of every field from this record
     * @param <String> $fieldName
     * @return <String>
     */
    public function getDisplayValue($fieldName) {
        $fieldValue = $this->get($fieldName);
        switch ($fieldName) {
        case 'modulename' :
            $fieldValue = vtranslate($fieldValue,  $this->module->getParentName().':'.$this->module->getName());
            break;
        default :
            break;
		}
		return $fieldValue;
    }

    public static function getInstanceById($id) {
        global $adb;
        
        $record = new self();
        if(empty($id)) {
            return $record;
        }

        $result = $adb->pquery("SELECT
                                    t.tabid as id,
                                    t.name,
                                    t.customized,
                                    en.fieldname as linkfield
                                FROM
                                    vtiger_tab t
                                    LEFT JOIN vtiger_entityname en ON en.tabid = t.tabid
                                WHERE
                                    t.tabid = ?
                                ORDER BY
                                    t.tabid
            ", array($id));

        if($adb->num_rows($result) > 0) {
            $record->set("id", $adb->query_result($result, 0, "id"));
            $record->set("tabid", $adb->query_result($result, 0, "id"));
            $record->set("name" ,$adb->query_result($result, 0, "name"));
            $record->set("customized", $adb->query_result($result, 0, "customized"));
            $record->set("linkfield", $adb->query_result($result, 0, "linkfield"));
            $record->id = $record->get("tabid");
        }

        return $record;
    }

    public function delete() {
        throw new Exception('Cannot delete module.');
    }

    public function save() {
        if(empty($this->id)) {
            if(!$this->isCreateModule()) {
                throw new Exception("Cannot create module, Duplicate module name.");
                return ;
            }
            $this->createModule();
        } else {
            $this->updateEntityName();
        }
        return $this->getId();
    }

    private function isCreateModule() {
        global $adb;
        $result = $adb->pquery("SELECT tabid FROM vtiger_tab WHERE name = ?", array($this->get('name')));
        if($adb->num_rows($result) > 0) {
            return false;
        }
        return true;
    }

    private function createModule() {
        global $adb;
        $table_name = 'vtiger_'.strtolower($this->get("name"));
        $main_id = strtolower($this->get("name")).'id';

        // モジュールの作成（DB）
        $module = new Vtiger_Module_Model();
        $module->name = $this->get("name");
        $module->parent = "Tools";
        $module->save();
        $module->initTables($table_name, $main_id);
        $this->id = $module->id;

        // 基本ブロックと項目の作成
        $blockInstance = new Vtiger_Block();
        $blockInstance->label = 'LBL_BASIC_INFORMATION';
        $module->addBlock($blockInstance);

        // 担当
        $field = new Vtiger_Field();
        $field->name = 'assigned_user_id';
        $field->table = 'vtiger_crmentity';
        $field->column = 'smownerid';
        $field->columntype = 'int(19)';
        $field->uitype = 53;
        $field->typeofdata = 'V~M';
        $field->masseditable = 0;
        $field->quickcreate = 0;
        $field->summaryfield = 1;
        $field->label = '担当';
        $blockInstance->addField($field);
        $field->setRelatedModules(Array('Users'));

        // 作成日時
        $field = new Vtiger_Field();
        $field->name = 'createdtime';
        $field->table = 'vtiger_crmentity';
        $field->column = 'createdtime';
        $field->uitype = 70;
        $field->typeofdata = 'D~O';
        $field->displaytype= 2;
        $field->masseditable = 0;
        $field->quickcreate = 0;
        $field->summaryfield = 0;
        $field->label= '作成日時';
        $blockInstance->addField($field);

        // 更新日時
        $field = new Vtiger_Field();
        $field->name = 'modifiedtime';
        $field->table = 'vtiger_crmentity';
        $field->column = 'modifiedtime';
        $field->columntype = 'datetime';
        $field->uitype = 70;
        $field->typeofdata = 'D~O';
        $field->masseditable = 0;
        $field->quickcreate = 0;
        $field->summaryfield = 0;
        $field->displaytype= 2;
        $field->label= '更新日時';
        $blockInstance->addField($field);

        $field = new Vtiger_Field();
        $field->name		= 'tags';
        $field->label		= 'tags';
        $field->table		= $module->basetable;
        $field->presence	= 2;
        $field->displaytype	= 6;
        $field->readonly	= 1;
        $field->uitype		= 1;
        $field->typeofdata	= 'V~O';
        $field->columntype	= 'VARCHAR(1)';
        $field->quickcreate	= 3;
        $field->masseditable= 0;
        $blockInstance->addField($field);

        $module->initWebservice();
        $module->setDefaultSharing('Public_ReadWriteDelete');

        $this->createFiles($module, $blockInstance);

        // メニューの追加
        $result = $adb->query("SELECT distinct appname FROM vtiger_app2tab");
        for($i=0; $i<$adb->num_rows($result); $i++) {
            $menu = $adb->query_result($result, $i, "appname");
            Settings_MenuEditor_Module_Model::addModuleToApp($module->name, $menu);
        }

        // ModTrackerの有効化
        require_once 'modules/ModTracker/ModTracker.php';
        ModTracker::enableTrackingForModule($module->id);

        // Allのフィルタの作成
        require_once 'setup/utils/FRFilterSetting.php';
        FRFilterSetting::add($module, 'All', array(
            'assigned_user_id',
            'createdtime',
            'modifiedtime',
        ), true);

        // インポート等の有効化
        $module->enableTools(array('Import', 'Export', 'Merge'));

        // コメントの有効化
        // $modules = array($module->name);
        // for( $i=0; $i<count($modules); $i++) {
        //     $modulename = $modules[$i];
        //     $moduleinstance = vtiger_module::getinstance($modulename);

        //     require_once 'modules/ModComments/ModComments.php';
        //     $commentsmodule = Vtiger_Module::getInstance( 'ModComments' );
        //     $fieldinstance = Vtiger_Field::getInstance( 'related_to', $commentsmodule );
        //     $fieldinstance->setRelatedModules( array($modulename) );
        //     $detailviewblock = ModComments::addWidgetTo( $modulename );
        // }

    }

    private function createFiles($module, $blockInstance) {
        // モジュールの作成（ファイル）
        if(!file_exists("modules/".$module->name)) {
            mkdir('modules/'.$module->name);
        }

        if(!file_exists('modules/'.$module->name.'/'.$module->name.'.php')) {
            $moduleFileContents = file_get_contents('vtlib/ModuleDir/6.0.0/ModuleName.php');
            $moduleFileContents = preg_replace('/ModuleName/', $module->name, $moduleFileContents);
            $moduleFileContents = preg_replace('/<modulename>/', strtolower($module->name), $moduleFileContents);
            file_put_contents('modules/'.$module->name.'/'.$module->name.'.php', $moduleFileContents);
        }

        if(!file_exists('languages/ja_jp/'.$module->name.'.php')) {
            $moduleLangContents = file_get_contents('vtlib/ModuleDir/6.0.0/languages/en_us/ModuleName.php');
            $moduleLangContents = preg_replace('/ModuleName/', $module->name, $moduleLangContents);
            $moduleLangContents = preg_replace('/Module Name/', $this->get('label'), $moduleLangContents);
            $moduleLangContents = preg_replace('/LBL_MODULEBLOCK_INFORMATION/', $blockInstance->label, $moduleLangContents);
            $moduleLangContents = preg_replace('/ModuleBlock Information/', 'Basic Information', $moduleLangContents);
            file_put_contents('languages/en_us/'.$module->name.'.php', $moduleFileContents);

            $moduleLangContents = preg_replace('/Basic Information/', '基本情報', $moduleLangContents);
            $moduleLangContents = preg_replace('/Custom Information/', '詳細情報', $moduleLangContents);
            file_put_contents('languages/ja_jp/'.$module->name.'.php', $moduleLangContents);
            }
    }

    private function updateEntityName() {
        global $adb;

        $module = Vtiger_Module_Model::getInstance($this->getId());
        $blockInstance = Vtiger_Block::getInstance('LBL_BASIC_INFORMATION');
        $this->createFiles($module, $blockInstance);

        $main_id = strtolower($this->get("name")).'id';

        $fields = $module->getFieldsByType(array('string', 'text', 'number', 'date', 'picklist', 'phone', 'email', 'url','salutation',));

        // Fieldの存在確認
        $linkfields = explode(',', $this->get('linkfield'));
        $targetFields = array();
        foreach($linkfields as $column) {
            $field = null;
            foreach($fields as $f) {
                if($f->get('column') == $column) {
                    $field = $f;
                    break;
                }
            }
            if(empty($field)) {
                throw new Exception('Unknown field : '.$column);
            }
            $targetFields[] = $field;
        }

        // 異なるテーブルのFieldは指定不可
        $table = "";
        foreach($targetFields as $f) {
            if(empty($table)) {
                $table = $f->table;
            } else if($table != $f->table) {
                throw new Exception('This item combination cannot be set : '.$this->get('linkfield'));
            }
        }

        // vtiger_entitynameを更新
        $result = $adb->pquery("SELECT fieldname FROM vtiger_entityname WHERE modulename = ?", array($this->get('name')));
        if($adb->num_rows($result) > 0) {
            $fieldname = $adb->query_result($result, 0, 'fieldname');
            if($fieldname == $this->get('linkfield')) {
                return ;// 同じなので何もしない
            }
            $adb->pquery("UPDATE vtiger_entityname SET tablename = ?, fieldname = ? WHERE modulename = ?
                ",array($table,$this->get('linkfield'),$this->get('name')));
        } else {
            // 既存モジュールに対しては正しくentityidfieldやentityidcolumnがセットされないので注意
            $adb->pquery("INSERT INTO vtiger_entityname(tabid,modulename,tablename,fieldname,entityidfield,entityidcolumn) VALUES(?,?,?,?,?,?)
                ",array($module->id, $module->name, $table, $this->get('linkfield'),$main_id, $main_id));
        }

        // labelを更新
        $this->updateLabel();
    }

    public function updateLabel() {
        global $adb;

        $module = Vtiger_Module_Model::getInstance($this->getId());
        $metainfo = Vtiger_Functions::getEntityModuleInfo($module);

        $table = $metainfo['tablename'];
        $idcolumn = $metainfo['entityidfield'];
        $columns  = explode(',', $metainfo['fieldname']);

        $column = count($columns) < 2? $columns[0] : sprintf("concat(%s)", implode(",' ',", $columns));

        $sql = sprintf('UPDATE vtiger_crmentity SET label = (SELECT %s FROM %s mod WHERE mod.%s = vtiger_crmentity.crmid)',
                 $column, $table, $idcolumn);

        $adb->pquery($sql, array());
    }

    /*
     * Function to get Edit view url 
     */
    public function getEditViewUrl() {
        return 'module=ModuleBuilder&parent=Settings&view=EditAjax&record='.$this->getId();
    }

    public function getRecordLinks() {
        $editLink = array(
            'linkurl' => "javascript:Settings_ModuleBuilder_Js.triggerEdit(event, '".$this->getId()."')",
            'linklabel' => 'LBL_EDIT',
            'linkicon' => 'icon-pencil'
        );
        $editLinkInstance = Vtiger_Link_Model::getInstanceFromValues($editLink);
        
        $deleteLink = array(
            'linkurl' => "javascript:Settings_ModuleBuilder_Js.triggerDelete(event,'".$this->getId()."')",
            'linklabel' => 'LBL_DELETE',
            'linkicon' => 'icon-trash'
        );
        $deleteLinkInstance = Vtiger_Link_Model::getInstanceFromValues($deleteLink);
        return array($editLinkInstance,$deleteLinkInstance);
    }
}

