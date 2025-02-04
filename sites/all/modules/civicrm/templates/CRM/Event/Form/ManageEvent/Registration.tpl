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
{if $addProfileBottomAdd OR $addProfileBottom}
  {if $addProfileBottomAdd}
  <table class="form-layout-compressed">
    <tr class="crm-event-manage-registration-form-block-additional_custom_post_{$profileBottomNumAdd}">
      <td scope="row" class="label" width="20%">{$form.additional_custom_post_id_multiple[$profileBottomNumAdd].label}</td>
      <td>{$form.additional_custom_post_id_multiple[$profileBottomNumAdd].html}	
          <span class='profile_bottom_add_link'>&nbsp;<a href="javascript:addProfileBottomAdd()">{ts}add profile{/ts}</a></span>
      </td>
    </tr>
  </table
  {/if}
  {if $addProfileBottom}
   <table class="form-layout-compressed">
     <tr class="crm-event-manage-registration-form-block-custom_post_{$profileBottomNum}">
       <td scope="row" class="label" width="20%">{$form.custom_post_id_multiple[$profileBottomNum].label}</td>
       <td>{$form.custom_post_id_multiple[$profileBottomNum].html}
           <span class='profile_bottom_link'>&nbsp;<a href="javascript:addProfileBottom()">{ts}add profile{/ts}</a></span>
       </td>
     </tr>
   </table
  {/if}
{else}
{assign var=eventID value=$id}
<div id="help">
{ts}If you want to provide an Online Registration page for this event, check the first box below and then complete the fields on this form.{/ts} 
{help id="id-event-reg"}
</div>
<span id="restmsg" class="msgok" style="display:none"></span>
<div class="crm-block crm-form-block crm-event-manage-registration-form-block">
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

    <div id="register">
     <table class="form-layout">
         <tr class="crm-event-manage-registration-form-block-is_online_registration">
            <td class="label">{$form.is_online_registration.label}</td>
            <td>{$form.is_online_registration.html}
            <span class="description">{ts}Enable or disable online registration for this event.{/ts}</span>
            </td>
         </tr>
     </table>
    </div>
    <div class="spacer"></div>
    <div id="registration_blocks">
	<table class="form-layout-compressed">
         
        <tr class="crm-event-manage-registration-form-block-registration_link_text">
            <td scope="row" class="label" width="20%">{$form.registration_link_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='registration_link_text' id=$eventID}{/if}</td>
            <td>{$form.registration_link_text.html} {help id="id-link_text"}</td>
        </tr>
       {if !$isTemplate}
        <tr class="crm-event-manage-registration-form-block-registration_start_date">  
           <td scope="row" class="label" width="20%">{$form.registration_start_date.label}</td>
           <td>{include file="CRM/common/jcalendar.tpl" elementName=registration_start_date}</td>
        </tr>
        <tr class="crm-event-manage-registration-form-block-registration_end_date">
           <td scope="row" class="label" width="20%">{$form.registration_end_date.label}</td>
           <td>{include file="CRM/common/jcalendar.tpl" elementName=registration_end_date}</td>
        </tr>
       {/if}
        <tr class="crm-event-manage-registration-form-block-is_multiple_registrations">
            <td scope="row" class="label" width="20%">{$form.is_multiple_registrations.label}</td>
            <td>{$form.is_multiple_registrations.html} {help id="id-allow_multiple"}</td>
        </tr>
        <tr class="crm-event-manage-registration-form-block-allow_same_participant_emails">
            <td scope="row" class="label" width="20%">{$form.allow_same_participant_emails.label}</td>
            <td>{$form.allow_same_participant_emails.html} {help id="id-allow_same_email"}</td>
        </tr>
        <tr class="crm-event-manage-registration-form-block-requires_approval">
          {if $form.requires_approval}
            <td scope="row" class="label" width="20%">{$form.requires_approval.label}</td>
            <td>{$form.requires_approval.html} {help id="id-requires_approval"}</td>
          {/if}
        </tr>
        <tr id="id-approval-text" class="crm-event-manage-registration-form-block-approval_req_text">
          {if $form.approval_req_text}
            <td scope="row" class="label" width="20%">{$form.approval_req_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='approval_req_text' id=$eventID}{/if}</td>
            <td>{$form.approval_req_text.html}</td>
          {/if}
        </tr>
        <tr class="crm-event-manage-registration-form-block-expiration_time">
            <td scope="row" class="label" width="20%">{$form.expiration_time.label}</td>
            <td>{$form.expiration_time.html|crmReplace:class:four} {help id="id-expiration_time"}</td>
        </tr>
    </table>
    <div class="spacer"></div>
    <div id="registration">
        {*Registration Block*}
        <div id="registration_screen_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('registration_screen_show'); show('registration_screen'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Registration Screen{/ts}</label><br />
        </div>	
        <div id="registration_screen">
        <fieldset><legend><a href="#" onclick= "hide('registration_screen'); show('registration_screen_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Registration Screen{/ts}</legend>
        <table class= "form-layout-compressed">
         <tr class="crm-event-manage-registration-form-block-intro_text">
            <td scope="row" class="label" width="20%">{$form.intro_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='intro_text' id=$eventID}{/if}</td>
            <td>{$form.intro_text.html}
            <div class="description">{ts}Introductory message / instructions for online event registration page (may include HTML formatting tags).{/ts}</div>
            </td>
         </tr>
         <tr class="crm-event-manage-registration-form-block-footer_text">
            <td scope="row" class="label" width="20%">{$form.footer_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='footer_text' id=$eventID}{/if}</td>
            <td>{$form.footer_text.html}
            <div class="description">{ts}Optional footer text for registration screen.{/ts}</div></td>
         </tr>
         <tr class="crm-event-manage-registration-form-block-custom_pre_id">
            <td scope="row" class="label" width="20%">{$form.custom_pre_id.label}</td>
            <td>{$form.custom_pre_id.html}<br />
            <span class="description">{ts}Include additional fields on this registration form by configuring and selecting a CiviCRM Profile to be included at the top of the page (immediately after the introductory message).{/ts}{help id="event-profile"}</span></td>
	 </tr>
         <tr class="crm-event-manage-registration-form-block-custom_post_id">
            <td scope="row" class="label" width="20%">{$form.custom_post_id.label}</td>
            <td>{$form.custom_post_id.html}
	        {if !$profilePostMultiple}
	          <span class='profile_bottom_link'>&nbsp;<a href="javascript:addProfileBottom()">{ts}add profile{/ts}</a></span>
		{/if}
	    <br />
            <span class="description">{ts}Include additional fields on this registration form by configuring and selecting a CiviCRM Profile to be included at the bottom of the page.{/ts}</span>
            </td>
        </tr>

         {if $profilePostMultiple}
         {foreach from=$profilePostMultiple item=profilePostId key=profilePostNum name=profilePostIdName}
 	    <tr class='crm-event-manage-registration-form-block-custom_post_multiple'>
               <td scope="row" class="label" width="20%">{$form.custom_post_id_multiple.$profilePostNum.label}</td>
               <td>{$form.custom_post_id_multiple.$profilePostNum.html}
	           {if $smarty.foreach.profilePostIdName.last}
	             <span class='profile_bottom_link'>&nbsp;<a href="javascript:addProfileBottom()">{ts}add profile{/ts}</a></span>
                   {/if}
	       </td>
             </tr>
         {/foreach}
 	{/if}
	<tr class='crm-event-manage-registration-form-block-custom_post_multiple'>
	    <td id="profile_bottom_multiple" colspan="2"></td>
        </tr>
        <tr id="additional_profile_pre" class="crm-event-manage-registration-form-block-additional_custom_pre_id">
            <td scope="row" class="label" width="20%">{$form.additional_custom_pre_id.label}</td>
            <td>{$form.additional_custom_pre_id.html}<br />
              <span class="description">{ts}Change this if you want to use a different profile for additional participants.{/ts}</span></td>
            </td>
        </tr>
        <tr id="additional_profile_post" class="crm-event-manage-registration-form-block-additional_custom_post_id">
             <td scope="row" class="label" width="20%">{$form.additional_custom_post_id.label}</td>
             <td>{$form.additional_custom_post_id.html}
	         {if !$profilePostMultipleAdd}<span class='profile_bottom_add_link'><a href="javascript:addProfileBottomAdd()">{ts}add profile{/ts}</a></span>
		 {/if}<br />
                <span class="description">{ts}Change this if you want to use a different profile for additional participants.{/ts}</span>
             </td>
        </tr>
	{if $profilePostMultipleAdd}
         {foreach from=$profilePostMultipleAdd item=profilePostIdA key=profilePostNumA name=profilePostIdAName}
 	    <tr class='crm-event-manage-registration-form-block-additional_custom_post_multiple'>
               <td scope="row" class="label" width="20%">{$form.additional_custom_post_id_multiple.$profilePostNumA.label}</td>
               <td>{$form.additional_custom_post_id_multiple.$profilePostNumA.html}
	           {if $smarty.foreach.profilePostIdAName.last}
		     <span class='profile_bottom_add_link'>&nbsp;<a href="javascript:addProfileBottomAdd()">{ts}add profile{/ts}</a></span>
                   {/if}
	       </td>
             </tr>
         {/foreach}
 	{/if}

	<tr class='crm-event-manage-registration-form-block-additional_custom_post_multiple'>
	    <td id="additional_profile_bottom_multiple" colspan="2"></td>
        </tr>
        </table>
        </fieldset>
        </div>

        {*Confirmation Block*}
        <div id="confirm_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('confirm_show'); show('confirm'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Screen{/ts}</label><br />
        </div>	

        <div id="confirm">
        <fieldset><legend><a href="#" onclick="hide('confirm'); show('confirm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Screen{/ts}</legend>
         <table class= "form-layout-compressed">
           <tr class="crm-event-manage-registration-form-block-confirm_title">
              <td scope="row" class="label" width="20%">{$form.confirm_title.label} <span class="marker">*</span> {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='confirm_title' id=$eventID}{/if}</td>
              <td>{$form.confirm_title.html}<br />
                  <span class="description">{ts}Page title for screen where user reviews and confirms their registration information.{/ts}</span>
              </td>
           </tr>
           <tr class="crm-event-manage-registration-form-block-confirm_text">
              <td scope="row" class="label" width="20%">{$form.confirm_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='confirm_text' id=$eventID}{/if}</td>
              <td>{$form.confirm_text.html}
                  <div class="description">{ts}Optional instructions / message for Confirmation screen.{/ts}</div> 
              </td>
           </tr>
           <tr class="crm-event-manage-registration-form-block-confirm_footer_text">
              <td scope="row" class="label" width="20%">{$form.confirm_footer_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='confirm_footer_text' id=$eventID}{/if}</td>
              <td>{$form.confirm_footer_text.html}
                 <div class="description">{ts}Optional page footer text for Confirmation screen.{/ts}</div>
              </td>
           </tr>
         </table>
        </fieldset>
        </div>

         {*ThankYou Block*}
        <div id="thankyou_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('thankyou_show'); show('thankyou'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Thank-you Screen{/ts}</label><br />
        </div>	

        <div id="thankyou">
        <fieldset><legend><a href="#" onclick="hide('thankyou'); show('thankyou_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Thank-you Screen{/ts}</legend>
         <table class= "form-layout-compressed">
           <tr class="crm-event-manage-registration-form-block-confirm_thankyou_title">           
              <td scope="row" class="label" width="20%">{$form.thankyou_title.label} <span class="marker">*</span> {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='thankyou_title' id=$eventID}{/if}</td>
              <td>{$form.thankyou_title.html}
                <div class="description">{ts}Page title for registration Thank-you screen.{/ts}</div>
            </td>
            </tr>
            <tr class="crm-event-manage-registration-form-block-confirm_thankyou_text">
              <td scope="row" class="label" width="20%">{$form.thankyou_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='thankyou_text' id=$eventID}{/if}</td>
              <td>{$form.thankyou_text.html}
                 <div class="description">{ts}Optional message for Thank-you screen (may include HTML formatting).{/ts}</div>
              </td>
            </tr>
            <tr class="crm-event-manage-registration-form-block-confirm_thankyou_footer_text">
              <td scope="row" class="label" width="20%">{$form.thankyou_footer_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='thankyou_footer_text' id=$eventID}{/if}</td>
              <td>{$form.thankyou_footer_text.html}
                  <div class="description">{ts}Optional footer text for Thank-you screen (often used to include links to other pages/activities on your site).{/ts}</div>
              </td>
            </tr>
         </table>
        </fieldset>
        </div>

        {* Confirmation Email Block *}
        <div id="mail_show" class="section-hidden section-hidden-border">
            <a href="#" onclick="hide('mail_show'); show('mail'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}Confirmation Email{/ts}</label><br />
        </div>	

        <div id="mail">
        <fieldset><legend><a href="#" onclick="hide('mail'); show('mail_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}Confirmation Email{/ts}</legend>
          <table class= "form-layout-compressed">
            <tr class="crm-event-manage-registration-form-block-is_email_confirm"> 
              <td scope="row" class="label" width="20%">{$form.is_email_confirm.label}</td>
              <td>{$form.is_email_confirm.html}<br />
                  <span class="description">{ts}Do you want a registration confirmation email sent automatically to the user? This email includes event date(s), location and contact information. For paid events, this email is also a receipt for their payment.{/ts}</span>
              </td>
            </tr>
          </table>
          <div id="confirmEmail">
           <table class="form-layout-compressed">
             <tr class="crm-event-manage-registration-form-block-confirm_email_text">
               <td scope="row" class="label" width="20%">{$form.confirm_email_text.label} {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='confirm_email_text' id=$eventID}{/if}</td>
               <td>{$form.confirm_email_text.html}<br />
                   <span class="description">{ts}Additional message or instructions to include in confirmation email.{/ts}</span>
               </td>
             </tr>
             <tr class="crm-event-manage-registration-form-block-confirm_from_name">
               <td scope="row" class="label" width="20%">{$form.confirm_from_name.label} <span class="marker">*</span> {if $action == 2}{include file='CRM/Core/I18n/Dialog.tpl' table='civicrm_event' field='confirm_from_name' id=$eventID}{/if}</td>
               <td>{$form.confirm_from_name.html}<br />
                   <span class="description">{ts}FROM name for email.{/ts}</span>
               </td>
             </tr>
             <tr class="crm-event-manage-registration-form-block-confirm_from_email">
               <td scope="row" class="label" width="20%">{$form.confirm_from_email.label} <span class="marker">*</span></td>
               <td>{$form.confirm_from_email.html}<br />
                   <span class="description">{ts}FROM email address (this must be a valid email account with your SMTP email service provider).{/ts}</span>
               </td>
             </tr>
             <tr class="crm-event-manage-registration-form-block-cc_confirm">
               <td scope="row" class="label" width="20%">{$form.cc_confirm.label}</td>
               <td>{$form.cc_confirm.html}<br />
                    <span class="description">{ts}You can notify event organizers of each online registration by specifying one or more email addresses to receive a carbon copy (cc). Multiple email addresses should be separated by a comma (e.g. jane@example.org, paula@example.org).{/ts}</span>
               </td>
             </tr>
               <td scope="row" class="label" width="20%">{$form.bcc_confirm.label}</td>
               <td>{$form.bcc_confirm.html}<br />
                  <span class="description">{ts}You may specify one or more email addresses to receive a blind carbon copy (bcc) of the confirmation email. Multiple email addresses should be separated by a comma (e.g. jane@example.org, paula@example.org).{/ts}</span>
               </td>
             </tr>
           </table>
	  </div>
        </fieldset>
        </div>
    </div> {*end of div registration_blocks*}
    </div>
 <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
 </div>

{include file="CRM/common/showHide.tpl"}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_online_registration"
trigger_value       ="" 
target_element_id   ="registration_blocks" 
target_element_type ="block"
field_type          ="radio"
invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_email_confirm"
trigger_value       =""
target_element_id   ="confirmEmail" 
target_element_type ="block"
field_type          ="radio"
invert              = 0
}
{include file="CRM/common/showHideByFieldValue.tpl" 
trigger_field_id    ="is_multiple_registrations"
trigger_value       =""
target_element_id   ="additional_profile_pre|additional_profile_post" 
target_element_type ="table-row"
field_type          ="radio"
invert              = 0
}
{if $form.requires_approval}
{include file="CRM/common/showHideByFieldValue.tpl" 
    trigger_field_id    ="requires_approval"
    trigger_value       =""
    target_element_id   ="id-approval-text" 
    target_element_type ="table-row"
    field_type          ="radio"
    invert              = 0
}
{/if}
<script type="text/javascript">
    {literal}
    cj("#is_multiple_registrations").change( function( ) {
        if ( !cj(this).attr( 'checked') ) {
            cj("#additional_custom_pre_id").val('');
            cj("#additional_custom_post_id").val('');
	    cj(".crm-event-manage-registration-form-block-additional_custom_post_multiple").hide();
        } else {
	    cj(".crm-event-manage-registration-form-block-additional_custom_post_multiple").show();
	}
    });
    
    showRuleFields( {/literal}{$ruleFields}{literal} );

    function showRuleFields( ruleFields ) 
    {	   
        var errorMsg = '{/literal}{ts 1="' + ruleFields + '"}In order to allow multiple registrations with the same email address, the default "Fuzzy" Dedupe Rule will be used to check for matching contact records and prevent duplicate registrations. This rule currently uses the following fields: %1. First and Last Name will be used to check for matches among the multiple participants being registered.{/ts}{literal}';

        //display error message.
        var imageIcon = "<a href='#' onclick='cj( \"#restmsg\" ).hide( ); return false;'>" + '<div class="ui-icon ui-icon-close" style="float:left"></div>' + '</a>';

        if ( cj("#allow_same_participant_emails").attr( 'checked' ) ) {
            cj( '#restmsg' ).html( imageIcon + errorMsg  ).show( );
        } else {
            cj( '#restmsg' ).html( imageIcon + errorMsg  ).hide( );
        }
    }

    var profileBottomCount = Number({/literal}{$profilePostMultiple|@count}{literal});

    function addProfileBottom( ) {
      profileBottomCount++;
      cj('.profile_bottom_link').css('display', 'none');
      var urlPath = {/literal}"{crmURL p='civicrm/event/manage/registration' h=0 q=$addProfileParams}"{literal};
      urlPath = urlPath + '&snippet=4&addProfileNum=' + profileBottomCount;
      cj.ajax({ url     : urlPath,
                async   : false,
                global  : false,
	        success : function ( content ) { 		
                 cj( "#profile_bottom_multiple" ).append( content );
                }
      });   
    }


    var profileBottomCountAdd = Number({/literal}{$profilePostMultipleAdd|@count}{literal});
    function addProfileBottomAdd( ) {
      profileBottomCountAdd++;
      cj('.profile_bottom_add_link').css('display', 'none');
      var urlPathAdd = {/literal}"{crmURL p='civicrm/event/manage/registration' h=0 q=$addProfileParamsAdd}"{literal};
      urlPathAdd = urlPathAdd + '&snippet=4&addProfileNumAdd=' + profileBottomCountAdd;
      cj.ajax({ url     : urlPathAdd,
                async   : false,
                global  : false,
	        success : function ( contentAdd ) { 		
                 cj( "#additional_profile_bottom_multiple" ).append( contentAdd );
                }
      });   
    }


    {/literal}
</script>
{include file="CRM/common/formNavigate.tpl"}
{/if}