{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{*Javascript function controls showing and hiding of form elements based on html type.*}
{literal}
<script type="text/Javascript">
    function option_html_type(form) { 
        var html_type = document.getElementById("html_type");
        var html_type_name = html_type.options[html_type.selectedIndex].value;

        if (html_type_name == "Text") {
            document.getElementById("price-block").style.display="block";
            document.getElementById("showoption").style.display="none";

        } else {
            document.getElementById("price-block").style.display="none";
            document.getElementById("showoption").style.display="block";
        }

        if (html_type_name == 'Radio' || html_type_name == 'CheckBox') {
	    cj("#optionsPerLine").show( );
        } else {
	    cj("#optionsPerLine").hide( );
	    cj("#optionsPerLineDef").hide( );
        }

        var radioOption, checkBoxOption;

        for (var i=1; i<=11; i++) {
            radioOption = 'radio'+i;
            checkBoxOption = 'checkbox'+i	
            if (html_type_name == 'Radio' || html_type_name == 'CheckBox' || html_type_name == 'Select') {
                if (html_type_name == "CheckBox") {
                    document.getElementById(checkBoxOption).style.display="block";
                    document.getElementById(radioOption).style.display="none";
                } else {
                    document.getElementById(radioOption).style.display="block";	
                    document.getElementById(checkBoxOption).style.display="none";
                }
            }
        }
	
    }
</script>
{/literal}
  <h3>{if $action eq 1}{ts}Add Field{/ts}{elseif $action eq 2}{ts}Edit Field{/ts}{/if}</h3>
  <div class="crm-block crm-form-block crm-price-field-form-block">
    <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
    <table class="form-layout">
        <tr class="crm-price-field-form-block-label">
           <td class="label">{$form.label.label}</td>
           <td>{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_price_field' field='label' id=$id}{/if}{$form.label.html}
           </td>
        </tr>
        <tr class="crm-price-field-form-block-html_type">
           <td class="label">{$form.html_type.label}</td>
           <td>{$form.html_type.html}
          </td>
        </tr>
        {if $action neq 4 and $action neq 2}
        <tr>
           <td>&nbsp;</td>
           <td class="description">{ts}Select the html type used to offer options for this field{/ts}
           </td>
        </tr>
        {/if}
 
    </table>
    <div class="spacer"></div>
    <div id="price-block" {if $action eq 2 && $form.html_type.value.0 eq 'Text'} class="show-block" {else} class="hide-block" {/if}>
        <table class="form-layout">
            <tr class="crm-price-field-form-block-price">
               <td class="label">{$form.price.label} <span class="crm-marker" title="{ts}This field is required.{/ts}">*</span></td>
               <td>{$form.price.html}
               {if $action neq 4}
                    <br /><span class="description">{ts}Unit price.{/ts}</span> {help id="id-negative"}
               {/if}
               </td>
            </tr>
	    {if $useForEvent}
	    <tr class="crm-price-field-form-block-count">
              <td class="label">{$form.count.label}</td>
              <td>{$form.count.html}<br />
                <span class="description">{ts}Enter a value here if you want to increment the number of registered participants per unit against the maximum number of participants allowed for this event.{/ts}</span>
                {help id="id-participant-count"}
              </td>
            </tr>
	    <tr class="crm-price-field-form-block-max_value">
              <td class="label">{$form.max_value.label}</td>
              <td>{$form.max_value.html}
              </td>
            </tr>
	    <tr class="crm-price-field-form-block-description">
              <td class="label">{$form.description.label}</td>
              <td>{$form.description.html}
              </td>
            </tr>
	  {/if}
        </table>
    </div>

    {if $action eq 1}
        {* Conditionally show table for setting up selection options - for field types = radio, checkbox or select *}
        <div id='showoption' class="hide-block">{ include file="CRM/Price/Form/OptionFields.tpl"}</div>
    {/if}
        <table class="form-layout">
            <tr id="optionsPerLine" class="crm-price-field-form-block-options_per_line">
	            <td class="label">{$form.options_per_line.label}</td>	
	            <td>{$form.options_per_line.html|crmReplace:class:two}</td>
            </tr>
            <tr class="crm-price-field-form-block-is_display_amounts">
               <td class="label">{$form.is_display_amounts.label}</td>
               <td>{$form.is_display_amounts.html}
                   {if $action neq 4}
                      <div class="description">{ts}Display amount next to each option? If no, then the amount should be in the option description.{/ts}</div>
                   {/if}
               </td>
            </tr>

            <tr class="crm-price-field-form-block-weight">
               <td class="label">{$form.weight.label}</td>
               <td>{$form.weight.html|crmReplace:class:two}
                    {if $action neq 4}
                        <div class="description">{ts}Weight controls the order in which fields are displayed in a group. Enter a positive or negative integer - lower numbers are displayed ahead of higher numbers.{/ts}</div>
                    {/if}
               </td>
            </tr>

           <tr class="crm-price-field-form-block-help_post">
              <td class="label">{$form.help_post.label}</td>
              <td>{if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_price_field' field='help_post' id=$id}{/if}{$form.help_post.html|crmReplace:class:huge}&nbsp;
                  {if $action neq 4}
                    <div class="description">{ts}Explanatory text displayed to users for this field.{/ts}</div>
                  {/if}
              </td>
           </tr>

        <tr class="crm-price-field-form-block-active_on">
           <td class="label">{$form.active_on.label}</td>
           <td>{include file="CRM/common/jcalendar.tpl" elementName=active_on}
           {if $action neq 4}
               <br /><span class="description">{ts}Date this field becomes effective (optional).  Used for price set fields that are made available starting on a specific date.{/ts}</span>
           {/if}
           </td>
        </tr>

        <tr class="crm-price-field-form-block-expire_on">
            <td class="label">{$form.expire_on.label}</td>
            <td>{include file="CRM/common/jcalendar.tpl" elementName=expire_on}
            {if $action neq 4}
                <br /><span class="description">{ts}Date this field expires (optional).  Used for price set fields that are no longer available after a specific date (e.g. early-bird pricing).{/ts}</span>
            {/if}
            </td>
        </tr>

        <tr class="crm-price-field-form-block-is_required">
           <td class="label">{$form.is_required.label}</td>
           <td>&nbsp;{$form.is_required.html}</td>
        </tr>
	    <tr class="crm-price-field-form-block-visibility_id">
           <td class="label">{$form.visibility_id.label}</td>
           <td>&nbsp;{$form.visibility_id.html}  {help id="id-visibility"}</td>
        </tr>
        <tr class="crm-price-field-form-block-is_active">
            <td class="label">{$form.is_active.label}</td>
            <td>{$form.is_active.html}</td>
        </tr>
     </table>
     <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
  </div>
 

<script type="text/javascript">
    option_html_type(this.form);
</script>

{* Give link to view/edit choice options if in edit mode and html_type is one of the multiple choice types *}
{if $action eq 2 AND ($form.data_type.value.1.0 eq 'CheckBox' OR $form.data_type.value.1.0 eq 'Radio' OR $form.data_type.value.1.0 eq 'Select') }
    <div class="action-link">
        <a href="{crmURL p="civicrm/admin/event/field/option" q="reset=1&action=browse&fid=`$id`"}" class="button"><span>{ts}Multiple Choice Options{/ts}</span></a>
    </div>
{/if}
