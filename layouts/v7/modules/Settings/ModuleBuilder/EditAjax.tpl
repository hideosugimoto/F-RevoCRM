{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    {assign var=RULE_MODEL_EXISTS value=true}
    {assign var=RULE_ID value=$RECORD_MODEL->getId()}
    {if empty($RULE_ID)}
        {assign var=RULE_MODEL_EXISTS value=false}
    {/if}
    <div class="moduleBuilderModalContainer modal-dialog modelContainer">
        {if $CURRENCY_MODEL_EXISTS}
            {assign var="HEADER_TITLE" value={vtranslate('LBL_EDIT_MODULE', $QUALIFIED_MODULE)}}
        {else}
            {assign var="HEADER_TITLE" value={vtranslate('LBL_ADD_NEW_MODULE', $QUALIFIED_MODULE)}}
        {/if}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <div class="modal-content">
            <form id="editCurrency" class="form-horizontal" method="POST">
                <input type="hidden" name="record" value="{$RECORD}" />
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="form-group">
                            <label class="control-label fieldLabel col-sm-5">{vtranslate('Module name', $QUALIFIED_MODULE)}&nbsp;<span class="redColor">*</span></label>
                            <div class="controls fieldValue col-xs-6">
                                <input type="text" class="inputElement" name="label" value="{vtranslate($RECORD_MODEL->get('name'),"Vtiger")}" data-rule-required = "true" {if !empty($RECORD_MODEL->get('name'))}readonly style="background:#ccc;"{/if} />
                            </div>	
                        </div>
                        <div class="form-group">
                            <label class="control-label fieldLabel col-sm-5">{vtranslate('Module system name', $QUALIFIED_MODULE)}&nbsp;<span class="redColor">*</span></label>
                            <div class="controls fieldValue col-xs-6">
                                <input type="text" class="inputElement" name="name" value="{$RECORD_MODEL->get('name')}" data-rule-required = "true" {if !empty($RECORD_MODEL->get('name'))}readonly style="background:#ccc;"{/if} />
                            </div>	
                        </div>
                        <div class="form-group">
                            <label class="control-label fieldLabel col-sm-5">{vtranslate('Linked fieldname', $QUALIFIED_MODULE)}</label>
                            <div class="controls fieldValue col-xs-6">
                                {if !empty($RECORD_MODEL->get('name'))}
                                {assign var="LINK_FIELDS" value=explode(',', $RECORD_MODEL->get('linkfield'))}
                                <select name="template" data-fieldname="template" data-fieldtype="picklist" class="inputElement select2" type="picklist" data-selected-value='{$RECORD_MODEL->get('linkfield')}'>
                                    <option value="">{vtranslate('LBL_SELECT_TEMPLATE', $QUALIFIED_MODULE)}
                                    {foreach key=KEY item=FIELD from=$FILEDS}
                                    {if $KEY == 'tags'}{continue}{/if}
                                    <option value="{$FIELD->get('column')}">{vtranslate($FIELD->get('label'), $RECORD_MODEL->get('name'))}
                                    {/foreach}
                                </select>
                                <input type="text" class="inputElement" name="linkfield" value="{$RECORD_MODEL->get('linkfield')}"><br>
                                {vtranslate('LBL_LINK_FIELD_INPUT_RULE', $QUALIFIED_MODULE)}
                                {else}
                                <span>{vtranslate('LBL_SAVED_AFTER_CHANGE_MESSAGE', $QUALIFIED_MODULE)}</span>
                                {/if}
                            </div>	
                        </div>
                </div>
            </div>
            {include file='ModalFooter.tpl'|@vtemplate_path:'Vtiger'}
        </form>
    </div>
</div>
{/strip}
