<?php
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/PickList/DependentPickListUtils.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('include/utils/CommonUtils.php');
include_once('includes/Loader.php');
include_once('includes/runtime/BaseModel.php');
include_once('includes/runtime/LanguageHandler.php');
include_once('modules/Vtiger/models/Record.php');
include_once('includes/runtime/Globals.php');
include_once('modules/Vtiger/models/Record.php');
include_once('modules/Vtiger/models/Module.php');

require_once('setup/utils/FRFieldSetting.php');
require_once('setup/utils/FRFilterSetting.php');



$purchaseOrderModuleInstance = Vtiger_Module::getInstance('PurchaseOrder');
$blockInstance = Vtiger_Block::getInstance('LBL_PO_INFORMATION', $purchaseOrderModuleInstance);
if ($blockInstance) {
    $fieldInstance = Vtiger_Field::getInstance('potential_id', $purchaseOrderModuleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name            = 'potential_id';
        $field->label            = 'Potential Name';
        $field->uitype            = 10;
        $field->typeofdata        = 'I~O';

        $blockInstance->addField($field);
        $field->setRelatedModules(array('Potentials'));

        $oppModuleModel = Vtiger_Module_Model::getInstance('Potentials');
        $oppModuleModel->setRelatedlist($purchaseOrderModuleInstance, 'PurchaseOrder', array('ADD'), 'get_dependents_list');
    }

    $fieldInstance = Vtiger_Field::getInstance('quote_id', $purchaseOrderModuleInstance);
    if (!$fieldInstance) {
        $field = new Vtiger_Field();
        $field->name            = 'quote_id';
        $field->label            = 'Quote Name';
        $field->uitype            = 10;
        $field->typeofdata        = 'I~O';

        $blockInstance->addField($field);
        $field->setRelatedModules(array('Quotes'));

        $oppModuleModel = Vtiger_Module_Model::getInstance('Quotes');
        $oppModuleModel->setRelatedlist($purchaseOrderModuleInstance, 'PurchaseOrder', array('ADD'), 'get_dependents_list');
    }
}
