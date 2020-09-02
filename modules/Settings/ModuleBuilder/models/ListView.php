<?php
class Settings_ModuleBuilder_ListView_Model extends Settings_Vtiger_ListView_Model {
	/**
	 * Function to get the list view entries
	 * @param Vtiger_Paging_Model $pagingModel
	 * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
	 */
	public function getListViewEntries($pagingModel) {
		global $adb;
		$module = new Settings_ModuleBuilder_Module_Model();

		$result = $adb->pquery("SELECT
									t.tabid,
									t.name,
									t.customized,
									en.fieldname as linkfield
								FROM
									vtiger_tab t
									LEFT JOIN vtiger_entityname en ON en.tabid = t.tabid
								WHERE
									t.isentitytype = 1
									AND t.name not in ('Events','Emails','Webmails','ModComments','SMSNotifier')
								ORDER BY
									t.tabid
				", array());

		$listViewRecordModels = array();
		for($i=0; $i<$adb->num_rows($result); $i++) {
			$record = new Settings_ModuleBuilder_Record_Model();
			$record->set("id", $adb->query_result($result, $i, "tabid"));
			$record->set("tabid", $adb->query_result($result, $i, "tabid"));
			$record->set("name" ,$adb->query_result($result, $i, "name"));
			$record->set("customized", $adb->query_result($result, $i, "customized"));
			$record->set("linkfield", $adb->query_result($result, $i, "linkfield"));

			$recordModuleName = $record->get('name');
			$record->set("label", vtranslate($recordModuleName, $recordModuleName));

            $recordModule = Vtiger_Module_Model::getInstance($record->get("tabid"));
			$recordFields = $recordModule->getFieldsByType(array('string', 'text', 'number', 'date', 'picklist', 'phone', 'email', 'url','salutation',));

			$linkfields = explode(',', $record->get('linkfield'));
			$linkfieldlabel = array();
			foreach($linkfields as $fieldname) {
			 	foreach($recordFields as $recordField) {
					$field = null;
			 		if($recordField->get('column') == $fieldname) {
			 			$field = $recordField;
			 			break;
			 		}
 			 	}
 			 	if(!empty($field)) {
 			 		$linkfieldlabel[] = vtranslate($field->get('label'), $recordModuleName);
 			 	}
			}
			$record->set("linkfieldlabel", implode(',', $linkfieldlabel));

			$record->id = $record->get("tabid");
			$listViewRecordModels[$record->getId()] = $record;
		}

		if($module->isPagingSupported()) {
			$pagingModel->calculatePageRange($listViewRecordModels);
			if(count($listViewRecordModels) > $pageLimit) {
				array_pop($listViewRecordModels);
				$pagingModel->set('nextPageExists', true);
			} else {
				$pagingModel->set('nextPageExists', false);
			}
		}
		return $listViewRecordModels;
	}

}