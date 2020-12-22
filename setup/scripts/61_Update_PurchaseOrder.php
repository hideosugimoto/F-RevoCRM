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


global $log;

$db = PearDatabase::getInstance();

$module_name = 'PurchaseOrder';
$module = Vtiger_Module::getInstance($module_name);

$blockInstance = Vtiger_Block::getInstance("LBL_PO_INFORMATION", $module);

// 案件名
$field = new Vtiger_Field();
$field->name = 'potential_id';
$field->table = 'vtiger_purchaseorder';
$field->column = 'potentialid';
$field->columntype = 'int';
$field->uitype = 76;
$field->typeofdata = 'I~O';
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->readonly = '1';
$field->displaytype = '1';
$field->masseditable = '1';
$field->label = 'Potential Name';
$blockInstance->addField($field);
$field->setRelatedModules(array('Potentials'));

$oppModuleModel = Vtiger_Module_Model::getInstance('Potentials');
$oppModuleModel->setRelatedlist($module, 'PurchaseOrder', array('ADD'), 'get_purchaseorder', $field->id);


// 顧客企業名
$field = new Vtiger_Field();
$field->name = 'account_id';
$field->table = 'vtiger_purchaseorder';
$field->column = 'accountid';
$field->columntype = 'int';
$field->uitype = 73;
$field->typeofdata = 'I~O';
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->readonly = '1';
$field->displaytype = '1';
$field->masseditable = '1';
$field->label = 'Account Name';
$blockInstance->addField($field);
$field->setRelatedModules(array('Accounts'));

$oppModuleModel = Vtiger_Module_Model::getInstance('Accounts');
$oppModuleModel->setRelatedlist($module, 'PurchaseOrder', array('ADD'), 'get_purchaseorder', $field->id);

//項目の並び替え
$fields = array(
    "subject",
    "potential_id",
    "purchaseorder_no",
    "vendor_id",
    "requisition_no",
    "tracking_no",
    "contact_id",
    "duedate",
    "carrier",
    "txtAdjustment",
    "salescommission",
    "exciseduty",
    "hdnGrandTotal",
    "hdnSubTotal",
    "hdnTaxType",
    "hdnS_H_Amount",
    "postatus",
    "account_id",
    "assigned_user_id",
    "createdtime",
    "modifiedtime",
    "currency_id",
    "conversion_rate",
    "modifiedby",
    "pre_tax_total",
    "paid",
    "balance",
    "source",
    "starred",
    "tags",
    "bill_country",
    "ship_country",
    "bill_code",
    "ship_code",
    "bill_state",
    "ship_state",
    "bill_city",
    "ship_city",
    "bill_street",
    "ship_street",
    "bill_pobox",
    "ship_pobox",
    "productid",
    "terms_conditions",
    "quantity",
    "listprice",
    "comment",
    "discount_amount",
    "discount_percent",
    "tax1",
    "tax2",
    "tax3",
    "hdnS_H_Percent",
    "hdnDiscountAmount",
    "hdnDiscountPercent",
    "image",
    "region_id",
    "tax4",
    "description",
    "enable_recurring",
    "recurring_frequency",
    "start_period",
    "end_period",
    "payment_duration",
    "paymentstatus",
    "last_recurring_date"
);

foreach ($fields as $key => $fieldname) {
    $db->query("UPDATE vtiger_field SET sequence = ".($key+1)." WHERE tabid = ".$module->id." AND fieldname = '".$fieldname."'");
}

//発注先名の必須を解除する。
$db->query("UPDATE vtiger_field SET typeofdata='I~O' WHERE fieldname='vendor_id' and tabid=$module->id");

echo"実行が完了しました。<br>";
