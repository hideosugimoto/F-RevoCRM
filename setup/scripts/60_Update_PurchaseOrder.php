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


/* 繰り返し支払情報 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'Recurring Payment Information';
$module->addBlock($blockInstance);

// 繰り返し有効
$field = new Vtiger_Field();
$field->name = 'enable_recurring';
$field->table = 'vtiger_purchaseorder';
$field->column = 'enable_recurring';
$field->columntype = 'int';
$field->uitype = 56;
$field->typeofdata = 'C~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Enable Recurring';
$blockInstance->addField($field);

// vtiger_payment_recurring_infoテーブルの作成
$table_name = 'vtiger_payment_recurring_info';
$main_id =  'purchaseorderid';
$module->initTables($table_name, $main_id);
// インデックスをはる
$sql = "ALTER TABLE $table_name ADD PRIMARY KEY (`$main_id`)";
$db->query($sql);

// 周期
$field = new Vtiger_Field();
$field->name = 'recurring_frequency';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'recurring_frequency';
$field->columntype = 'varchar(200)';
$field->uitype = 16;
$field->typeofdata = 'V~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Frequency';
$array = array("Daily","Weekly","Monthly","Quarterly","Yearly");
$field->setPicklistValues( $array );
$blockInstance->addField($field);

// 開始時期
$field = new Vtiger_Field();
$field->name = 'start_period';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'start_period';
$field->columntype = 'date';
$field->uitype = 5;
$field->typeofdata = 'D~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Start Period';
$blockInstance->addField($field);

// 終了時期
$field = new Vtiger_Field();
$field->name = 'end_period';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'end_period';
$field->columntype = 'date';
$field->uitype = 5;
$field->typeofdata = 'D~O~OTH~G~start_period~Start Period';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'End Period';
$blockInstance->addField($field);

// 支払い期限
$field = new Vtiger_Field();
$field->name = 'payment_duration';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'payment_duration';
$field->columntype = 'varchar(200)';
$field->uitype = 16;
$field->typeofdata = 'V~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Payment Duration';
$array = array("Net 01 day","Net 05 days","Net 07 days","Net 10 days","Net 15 days","Net 30 days","Net 45 days","Net 60 days");
$field->setPicklistValues($array);
$blockInstance->addField($field);

// 支払ステータス
$field = new Vtiger_Field();
$field->name = 'paymentstatus';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'payment_status';
$field->columntype = 'varchar(200)';
$field->uitype = 15;
$field->typeofdata = 'V~M';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Payment Status';
$array = array("AutoCreated","登録済み","Cancel","承認済み","Sent","Credit Payment","Paid");
$field->setPicklistValues($array);
$blockInstance->addField($field);

// 次回支払日
$field = new Vtiger_Field();
$field->name = 'last_recurring_date';
$field->table = 'vtiger_payment_recurring_info';
$field->column = 'last_recurring_date';
$field->columntype = 'date';
$field->uitype = 5;
$field->typeofdata = 'D~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->displaytype = 2;
$field->label = 'Next Payment Date';
$blockInstance->addField($field);




vimport('~~include/events/include.inc');
$em = new VTEventsManager($db);

// Registering event for Recurring Invoices
$em->registerHandler('vtiger.entity.aftersave', 'modules/PurchaseOrder/RecurringPaymentHandler.php', 'RecurringPaymentHandler');


//繰り返し支払用のCronを設定する
vimport('vtlib/Vtiger/Cron.php');
Vtiger_Cron::register( 'RecurringPayment', 'cron/modules/PurchaseOrder/RecurringPayment.service', 43200, 'PurchaseOrder', 1, 8, 'Recommended frequency for RecurringPayment is 12 hours');

echo "実行が完了しました。<br>";
