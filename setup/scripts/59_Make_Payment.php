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

$module_name = 'Payment';
$table_name = 'vtiger_payment';
//$main_name = 'dailyreportsname';
$main_id =  'paymentid';

$module = new Vtiger_Module();
$module->name = $module_name;
$module->parent = "Inventory";
$module->save();
$module->initTables($table_name, $main_id);
$tabid = $module->id;

// インデックスをはる
$sql = "ALTER TABLE $table_name ADD PRIMARY KEY (`$main_id`)";
$db->query($sql);
$sql = "ALTER TABLE ".$table_name."cf ADD PRIMARY KEY(`$main_id`)";
$db->query($sql);

/* 基本情報 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_PAYMENT_INFORMATION';
$module->addBlock($blockInstance);

// タイトル
$field = new Vtiger_Field();
$field->name = 'subject';
$field->table = 'vtiger_payment';
$field->column = 'subject';
$field->columntype = 'varchar(100)';
$field->uitype = 2;
$field->typeofdata = 'V~M';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Subject';
$blockInstance->addField($field);

/*
* モジュール内でキーとなるカラム1つに対して実行
* 複数回は実施しないこと
*/
$module->setEntityIdentifier($field);


$table_name = 'vtiger_paymentbillads';
$main_id =  'paymentbilladdressid';
$module->initTables($table_name, $main_id);
// インデックスをはる
$sql = "ALTER TABLE $table_name ADD PRIMARY KEY (`$main_id`)";
$db->query($sql);

$table_name = 'vtiger_paymentshipads';
$main_id =  'paymentshipaddressid';
$module->initTables($table_name, $main_id);
// インデックスをはる
$sql = "ALTER TABLE $table_name ADD PRIMARY KEY (`$main_id`)";
$db->query($sql);


// 発注
$field = new Vtiger_Field();
$field->name = 'purchaseorder_id';
$field->table = 'vtiger_payment';
$field->column = 'purchaseorderid';
$field->columntype = 'int';
$field->uitype = 10;
$field->typeofdata = 'I~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'PurchaseOrder';
$blockInstance->addField($field);
$field->setRelatedModules(array('PurchaseOrder'));

$oppModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');
$oppModuleModel->setRelatedlist($module, 'Payment', array('ADD'), 'get_payments', $field->id);

// 支払番号
$field = new Vtiger_Field();
$field->name = 'payment_no';
$field->table = 'vtiger_payment';
$field->column = 'payment_no';
$field->columntype = 'varchar(100)';
$field->uitype = 4;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Payment No';
$blockInstance->addField($field);

// 顧客番号
$field = new Vtiger_Field();
$field->name = 'customerno';
$field->table = 'vtiger_payment';
$field->column = 'customerno';
$field->columntype = 'varchar(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Customer No';
$blockInstance->addField($field);

// ご担当者様名
$field = new Vtiger_Field();
$field->name = 'contact_id';
$field->table = 'vtiger_payment';
$field->column = 'contactid';
$field->columntype = 'int';
$field->uitype = 57;
$field->typeofdata = 'I~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Contact Name';
$blockInstance->addField($field);

$oppModuleModel = Vtiger_Module_Model::getInstance('Contacts');
$oppModuleModel->setRelatedlist($module, 'Payment', array('ADD'), 'get_payments', $field->id);

// 支払日
$field = new Vtiger_Field();
$field->name = 'paymentdate';
$field->table = 'vtiger_payment';
$field->column = 'paymentdate';
$field->columntype = 'date';
$field->uitype = 5;
$field->typeofdata = 'D~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Payment Date';
$blockInstance->addField($field);

// 期限日
$field = new Vtiger_Field();
$field->name = 'duedate';
$field->table = 'vtiger_payment';
$field->column = 'duedate';
$field->columntype = 'date';
$field->uitype = 5;
$field->typeofdata = 'D~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Due Date';
$blockInstance->addField($field);

// 調整
$field = new Vtiger_Field();
$field->name = 'txtAdjustment';
$field->table = 'vtiger_payment';
$field->column = 'adjustment';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'NN~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Adjustment';
$blockInstance->addField($field);

// 消費税
$field = new Vtiger_Field();
$field->name = 'exciseduty';
$field->table = 'vtiger_payment';
$field->column = 'exciseduty';
$field->columntype = 'decimal(25,3)';
$field->uitype = 1;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Excise Duty';
$blockInstance->addField($field);

// 小計
$field = new Vtiger_Field();
$field->name = 'hdnSubTotal';
$field->table = 'vtiger_payment';
$field->column = 'subtotal';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Sub Total';
$blockInstance->addField($field);

// 営業手数料
$field = new Vtiger_Field();
$field->name = 'salescommission';
$field->table = 'vtiger_payment';
$field->column = 'salescommission';
$field->columntype = 'decimal(25,3)';
$field->uitype = 1;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Sales Commission';
$blockInstance->addField($field);

// 合計
$field = new Vtiger_Field();
$field->name = 'hdnGrandTotal';
$field->table = 'vtiger_payment';
$field->column = 'total';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 1;
$field->displaytype = 3;
$field->label = 'Total';
$blockInstance->addField($field);

// 税種別
$field = new Vtiger_Field();
$field->name = 'hdnTaxType';
$field->table = 'vtiger_payment';
$field->column = 'taxtype';
$field->columntype = 'varchar(25)';
$field->uitype = 16;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Tax Type';
$blockInstance->addField($field);

// 顧客企業名
$field = new Vtiger_Field();
$field->name = 'account_id';
$field->table = 'vtiger_payment';
$field->column = 'accountid';
$field->columntype = 'int';
$field->uitype = 73;
$field->typeofdata = 'I~M';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Account Name';
$blockInstance->addField($field);

$oppModuleModel = Vtiger_Module_Model::getInstance('Accounts');
$oppModuleModel->setRelatedlist($module, 'Payment', array('ADD'), 'get_payments', $field->id);

// ステータス
$field = new Vtiger_Field();
$field->name = 'paymentstatus';
$field->table = 'vtiger_payment';
$field->column = 'paymentstatus';
$field->columntype = 'varchar(200)';
$field->uitype = 15;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Status';
$array = array("AutoCreated","登録済み","Cancel","承認済み","Sent","Credit Payment","Paid");
$field->setPicklistValues( $array );
$blockInstance->addField($field);

// 担当
$field = new Vtiger_Field();
$field->name = 'assigned_user_id';
$field->table = 'vtiger_crmentity';
$field->column = 'smownerid';
$field->columntype = 'int';
$field->uitype = 53;
$field->typeofdata = 'V~M';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Assigned To';
$blockInstance->addField($field);

// 登録日時
$field = new Vtiger_Field();
$field->name = 'createdtime';
$field->table = 'vtiger_crmentity';
$field->column = 'createdtime';
$field->columntype = 'datetime';
$field->uitype = 70;
$field->typeofdata = 'DT~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 2;
$field->label = 'Created Time';
$blockInstance->addField($field);

// 最終更新日時
$field = new Vtiger_Field();
$field->name = 'modifiedtime';
$field->table = 'vtiger_crmentity';
$field->column = 'modifiedtime';
$field->columntype = 'datetime';
$field->uitype = 70;
$field->typeofdata = 'DT~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 2;
$field->label = 'Modified Time';
$blockInstance->addField($field);

// 通貨
$field = new Vtiger_Field();
$field->name = 'currency_id';
$field->table = 'vtiger_payment';
$field->column = 'currency_id';
$field->columntype = 'int';
$field->uitype = 117;
$field->typeofdata = 'I~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->defaultvalue = 1;
$field->label = 'Currency';
$blockInstance->addField($field);

// コンバージョン率
$field = new Vtiger_Field();
$field->name = 'conversion_rate';
$field->table = 'vtiger_payment';
$field->column = 'conversion_rate';
$field->columntype = 'decimal(10,3)';
$field->uitype = 1;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Conversion Rate';
$blockInstance->addField($field);

// 最終更新者
$field = new Vtiger_Field();
$field->name = 'modifiedby';
$field->table = 'vtiger_crmentity';
$field->column = 'modifiedby';
$field->columntype = 'int';
$field->uitype = 52;
$field->typeofdata = 'V~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Last Modified By';
$blockInstance->addField($field);

// 税引前合計
$field = new Vtiger_Field();
$field->name = 'pre_tax_total';
$field->table = 'vtiger_payment';
$field->column = 'pre_tax_total';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Pre Tax Total';
$blockInstance->addField($field);

// 受領済み
$field = new Vtiger_Field();
$field->name = 'received';
$field->table = 'vtiger_payment';
$field->column = 'received';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Received';
$blockInstance->addField($field);

// 未払い残高
$field = new Vtiger_Field();
$field->name = 'balance';
$field->table = 'vtiger_payment';
$field->column = 'balance';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'Balance';
$blockInstance->addField($field);

// 案件名
$field = new Vtiger_Field();
$field->name = 'potential_id';
$field->table = 'vtiger_payment';
$field->column = 'potential_id';
$field->columntype = 'varchar(100)';
$field->uitype = 10;
$field->typeofdata = 'I~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Potential Name';
$blockInstance->addField($field);
$field->setRelatedModules(array('Potentials'));

$oppModuleModel = Vtiger_Module_Model::getInstance('Potentials');
$oppModuleModel->setRelatedlist($module, 'Payment', array('ADD'), 'get_dependents_list', $field->id);


// 入力方法
$field = new Vtiger_Field();
$field->name = 'source';
$field->table = 'vtiger_crmentity';
$field->column = 'source';
$field->columntype = 'varchar(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 2;
$field->label = 'Source';
$blockInstance->addField($field);

// フォロー
$field = new Vtiger_Field();
$field->name = 'starred';
$field->table = 'vtiger_crmentity_user_field';
$field->column = 'starred';
$field->columntype = 'varchar(100)';
$field->uitype = 56;
$field->typeofdata = 'C~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 6;
$field->label = 'starred';
$blockInstance->addField($field);

// tags
$field = new Vtiger_Field();
$field->name = 'tags';
$field->table = 'vtiger_payment';
$field->column = 'tags';
$field->columntype = 'varchar(1)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 0;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 6;
$field->label = 'tags';
$blockInstance->addField($field);

/* 項目の詳細 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_ITEM_DETAILS';
$module->addBlock($blockInstance);

// 品目名
$field = new Vtiger_Field();
$field->name = 'productid';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'productid';
$field->columntype = 'int';
$field->uitype = 10;
$field->typeofdata = 'V~M';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Item Name';
$blockInstance->addField($field);
$field->setRelatedModules(array('Products'));

$oppModuleModel = Vtiger_Module_Model::getInstance('Products');
$oppModuleModel->setRelatedlist($module, 'Payment', array('ADD'), 'get_payments', $field->id);

// 数量
$field = new Vtiger_Field();
$field->name = 'quantity';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'quantity';
$field->columntype = 'decimal(25,3)';
$field->uitype = 7;
$field->typeofdata = 'N~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Quantity';
$blockInstance->addField($field);

// 定価
$field = new Vtiger_Field();
$field->name = 'listprice';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'listprice';
$field->columntype = 'decimal(27,8)';
$field->uitype = 71;
$field->typeofdata = 'N~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'List Price';
$blockInstance->addField($field);

// 品目のコメント
$field = new Vtiger_Field();
$field->name = 'comment';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'comment';
$field->columntype = 'text';
$field->uitype = 19;
$field->typeofdata = 'V~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Item Comment';
$blockInstance->addField($field);

// 品目の割引額
$field = new Vtiger_Field();
$field->name = 'discount_amount';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'discount_amount';
$field->columntype = 'decimal(27,8)';
$field->uitype = 71;
$field->typeofdata = 'N~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Item Discount Amount';
$blockInstance->addField($field);

// 品目の割引率
$field = new Vtiger_Field();
$field->name = 'discount_percent';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'discount_percent';
$field->columntype = 'decimal(7,3)';
$field->uitype = 7;
$field->typeofdata = 'V~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Item Discount Percent';
$blockInstance->addField($field);

// VAT
$field = new Vtiger_Field();
$field->name = 'tax1';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'tax1';
$field->columntype = 'decimal(7,3)';
$field->uitype = 83;
$field->typeofdata = 'V~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'VAT';
$blockInstance->addField($field);

// 売上高
$field = new Vtiger_Field();
$field->name = 'tax2';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'tax2';
$field->columntype = 'decimal(7,3)';
$field->uitype = 83;
$field->typeofdata = 'V~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Sales';
$blockInstance->addField($field);

// サービス
$field = new Vtiger_Field();
$field->name = 'tax3';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'tax3';
$field->columntype = 'decimal(7,3)';
$field->uitype = 83;
$field->typeofdata = 'V~O';
$field->quickcreate = 0;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = 'Service';
$blockInstance->addField($field);

// 送料と取扱手数料の課税
$field = new Vtiger_Field();
$field->name = 'hdnS_H_Percent';
$field->table = 'vtiger_payment';
$field->column = 's_h_percent';
$field->columntype = 'decimal(25,8)';
$field->uitype = 1;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'S&H Percent';
$blockInstance->addField($field);

// 値引き額
$field = new Vtiger_Field();
$field->name = 'hdnDiscountAmount';
$field->table = 'vtiger_payment';
$field->column = 'discount_amount';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Discount Amount';
$blockInstance->addField($field);

// 値引き率
$field = new Vtiger_Field();
$field->name = 'hdnDiscountPercent';
$field->table = 'vtiger_payment';
$field->column = 'discount_percent';
$field->columntype = 'decimal(25,3)';
$field->uitype = 1;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Discount Percent';
$blockInstance->addField($field);

// 課税対象地域
$field = new Vtiger_Field();
$field->name = 'region_id';
$field->table = 'vtiger_payment';
$field->column = 'region_id';
$field->columntype = 'int';
$field->uitype = 16;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->readonly = 0;
$field->displaytype = 5;
$field->label = 'Tax Region';
$blockInstance->addField($field);

// 消費税
$field = new Vtiger_Field();
$field->name = 'tax4';
$field->table = 'vtiger_inventoryproductrel';
$field->column = 'tax4';
$field->columntype = 'decimal(7,3)';
$field->uitype = 83;
$field->typeofdata = 'V~O';
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->readonly = '0';
$field->displaytype = '5';
$field->masseditable = '0';
$field->label = '消費税';
$blockInstance->addField($field);

/* 住所情報 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_ADDRESS_INFORMATION';
$module->addBlock($blockInstance);

// 郵便番号 (支払先)
$field = new Vtiger_Field();
$field->name = 'bill_code';
$field->table = 'vtiger_paymentbillads';
$field->column = 'bill_code';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Billing Code';
$blockInstance->addField($field);

// 郵便番号(出荷先)
$field = new Vtiger_Field();
$field->name = 'ship_code';
$field->table = 'vtiger_paymentshipads';
$field->column = 'ship_code';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Shipping Code';
$blockInstance->addField($field);

// 都道府県 (支払先)
$field = new Vtiger_Field();
$field->name = 'bill_state';
$field->table = 'vtiger_paymentbillads';
$field->column = 'bill_state';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Billing State';
$blockInstance->addField($field);

// 都道府県(出荷先)
$field = new Vtiger_Field();
$field->name = 'ship_state';
$field->table = 'vtiger_paymentshipads';
$field->column = 'ship_state';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Shipping State';
$blockInstance->addField($field);

// 市町村区 (支払先)
$field = new Vtiger_Field();
$field->name = 'bill_city';
$field->table = 'vtiger_paymentbillads';
$field->column = 'bill_city';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Billing City';
$blockInstance->addField($field);

// 市町村区(出荷先)
$field = new Vtiger_Field();
$field->name = 'ship_city';
$field->table = 'vtiger_paymentshipads';
$field->column = 'ship_city';
$field->columntype = 'varchar(30)';
$field->uitype = 1;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Shipping City';
$blockInstance->addField($field);

// 番地 (支払先)
$field = new Vtiger_Field();
$field->name = 'bill_street';
$field->table = 'vtiger_paymentbillads';
$field->column = 'bill_street';
$field->columntype = 'varchar(250)';
$field->uitype = 24;
$field->typeofdata = 'V~M';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Billing Address';
$blockInstance->addField($field);

// 番地(出荷先)
$field = new Vtiger_Field();
$field->name = 'ship_street';
$field->table = 'vtiger_paymentshipads';
$field->column = 'ship_street';
$field->columntype = 'varchar(250)';
$field->uitype = 24;
$field->typeofdata = 'V~M';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 0;
$field->label = 'Shipping Address';
$blockInstance->addField($field);

// 送料と取扱手数料
$field = new Vtiger_Field();
$field->name = 'hdnS_H_Amount';
$field->table = 'vtiger_payment';
$field->column = 's_h_amount';
$field->columntype = 'decimal(25,8)';
$field->uitype = 72;
$field->typeofdata = 'N~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 3;
$field->label = 'S&H Amount';
$blockInstance->addField($field);

/* 諸条件 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_TERMS_INFORMATION';
$module->addBlock($blockInstance);

// 諸条件
$field = new Vtiger_Field();
$field->name = 'terms_conditions';
$field->table = 'vtiger_payment';
$field->column = 'terms_conditions';
$field->columntype = 'text';
$field->uitype = 19;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 1;
$field->summaryfield = 1;
$field->label = 'Terms & Conditions';
$blockInstance->addField($field);

/* 詳細情報 */
$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_DESCRIPTION_INFORMATION';
$module->addBlock($blockInstance);

// 詳細内容
$field = new Vtiger_Field();
$field->name = 'description';
$field->table = 'vtiger_crmentity';
$field->column = 'description';
$field->columntype = 'longtext';
$field->uitype = 19;
$field->typeofdata = 'V~O';
$field->masseditable = 1;
$field->quickcreate = 3;
$field->summaryfield = 0;
$field->displaytype = 1;
$field->label = 'Description';
$blockInstance->addField($field);


// 初期共有設定を行う
// 本設定はモジュール内全てのデータを公開
$module->initWebservice();
$module->setDefaultSharing('Public_ReadWriteDelete');

//必須
Settings_MenuEditor_Module_Model::addModuleToApp($module->name, $module->parent);

//ModTrackerに追跡
$module = Vtiger_Module::getInstance($module_name);
ModTracker::enableTrackingForModule($module->id);

//　************　一覧の設定　************
FRFilterSetting::deleteAll($module);
FRFilterSetting::add($module, 'All', array(
    'subject',
    'purchaseorder_id',
    'payment_no',
    'contact_id',
    'hdnGrandTotal',
    'account_id',
    'paymentstatus',
    'assigned_user_id',
), true);


//更新履歴の関連付け
$module = Vtiger_Module::getInstance($module_name);
ModTracker::enableTrackingForModule($module->id);

//インポート等の有効化
$module = Vtiger_Module::getInstance('Payment');
$module->enableTools(array('Import', 'Export', 'Merge'));

/**
 * ModCommentsモジュールを関連に追加
 */
$log->debug("[START] Add Comments function");
$modules = array('Payment');
for( $i=0; $i<count($modules); $i++) {
    $modulename = $modules[$i];
    $moduleinstance = vtiger_module::getinstance($modulename);

    require_once 'modules/ModComments/ModComments.php';
    $commentsmodule = Vtiger_Module::getInstance( 'ModComments' );
    $fieldinstance = Vtiger_Field::getInstance( 'related_to', $commentsmodule );
    $fieldinstance->setRelatedModules( array($modulename) );
    $detailviewblock = ModComments::addWidgetTo( $modulename );
    echo "comment widget for module $modulename has been created";
}
$log->debug("[END] Add Comments function");


$db->query("ALTER TABLE vtiger_payment ADD COLUMN compound_taxes_info TEXT");


// 予定表の関連メニューを追加
$relatedmodule = Vtiger_Module::getInstance('Calendar');
$module->setRelatedlist($relatedmodule, 'Activities', array('ADD'), 'get_activities');

//ドキュメントの関連メニューを追加
$relatedmodule = Vtiger_Module::getInstance('Documents');
$module->setRelatedlist($relatedmodule, 'Documents', array('ADD','SELECT'), 'get_attachments');

//サービスに支払の関連メニューを追加
$sourcemodule = Vtiger_Module::getInstance('Services');
$sourcemodule->setRelatedlist($module, 'Payment', array('ADD'), 'get_payments');

//活動の関連項目の選択肢に支払モジュールを追加
$db->query("insert into vtiger_ws_referencetype values(34,'Payment')");

echo "実行が完了しました。<br>";
