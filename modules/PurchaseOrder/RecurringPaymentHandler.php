<?php
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/

require_once('include/utils/utils.php');

class RecurringPaymentHandler extends VTEventHandler {
	
	private $entityData;
	
	public function handleEvent($handlerType, $entityData) {
		$this->entityData = $entityData;
	
		if ($this->isPurchaseOrderModule()) {
			$this->handleRecurringPaymentGeneration();
		}
	}
	
	private function handleRecurringPaymentGeneration() {
		if ($this->isRecurringPaymentEnabled()) {
			if (empty($this->getNextPaymentDate()) || $this->isStartDateAfterNextPaymentDate()) {
				$this->setNextPaymentDateEqualsToStartDate();
			}
		} else {
			$this->deleteRecurringPaymentData();
		}
	}
	
	private function isStartDateAfterNextPaymentDate()
	{
		$startPeriod = new DateTime($this->getStartDate());
		$nextPaymentDate = new DateTime($this->getNextPaymentDate());
		
		return $startPeriod > $nextPaymentDate;
	}
	
	private function isPurchaseOrderModule() {
		return $this->entityData->getModuleName() == 'PurchaseOrder';
	}
	
	private function getStartDate()
	{
		$data = $this->entityData->getData();
		return DateTimeField::convertToDBFormat($data['start_period']);
	}
	
	private function getNextPaymentDate() {
		$data = $this->entityData->getData();
		return $data['last_recurring_date'];
	}
	
	private function isRecurringPaymentEnabled() {
		$data = $this->entityData->getData();
		return !empty($data['enable_recurring']);
	}
	
	private function setNextPaymentDateEqualsToStartDate()
	{
		$db = PearDatabase::getInstance();
		$query = "UPDATE vtiger_payment_recurring_info SET last_recurring_date = start_period WHERE purchaseorderid = ?";
		$db->pquery($query, [$this->entityData->getId()]);
	}
	
	private function deleteRecurringPaymentData()
	{
		$db = PearDatabase::getInstance();
		$query = "DELETE FROM vtiger_payment_recurring_info WHERE purchaseorderid = ?";
		$db->pquery($query, [$this->entityData->getId()]);	
	}
}
