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
{* CiviCase DashBoard (launch page) *}

<div class="crm-block crm-content-block">
{if $notConfigured} {* Case types not present. Component is not configured for use. *}
    {include file="CRM/Case/Page/ConfigureError.tpl"}
{else}

{capture assign=newCaseURL}{crmURL p="civicrm/contact/view/case" q="action=add&context=standalone&reset=1"}{/capture}

<div class="crm-submit-buttons">
    {if $newClient and $allowToAddNewCase}	
	    <a href="{$newCaseURL}" class="button"><span><div class="icon add-icon"></div> {ts}Add Case{/ts}</span></a>
    {/if}
    <div class="crm-case-dashboard-switch-view-buttons">
        {if $myCases}
            {* check for access all cases and activities *}
            {if call_user_func(array('CRM_Core_Permission','check'), 'access all cases and activities')}
                <a class="button" href="{crmURL p="civicrm/case" q="reset=1&all=1"}"><span>{ts}All Cases with Upcoming Activities{/ts}</span></a>
            {/if}
        {else}
            <a class="button" href="{crmURL p="civicrm/case" q="reset=1&all=0"}"><span>{ts}My Cases with Upcoming Activities{/ts}</span></a>
        {/if}
        <a class="button" href="{crmURL p="civicrm/case/search" q="reset=1&case_owner=1&force=1"}"><span>{ts}My Cases{/ts}</span></a>
    </div>
</div>


<h3>
{if $myCases}
  {ts}Summary of Case Involvement{/ts}
{else}
  {ts}Summary of All Cases{/ts}
{/if}
</h3>
<table class="report">
  <tr class="columnheader">
    <th>&nbsp;</th>
    {foreach from=$casesSummary.headers item=header}
    <th scope="col" class="right" style="padding-right: 10px;"><a href="{$header.url}">{$header.status}</a></th>
    {/foreach}
  </tr>
  {foreach from=$casesSummary.rows item=row key=caseType}
   <tr class="crm-case-caseStatus">
   <th><strong>{$caseType}</strong></th>
   {foreach from=$casesSummary.headers item=header}
    {assign var="caseStatus" value=$header.status}
    <td class="label">
    {if $row.$caseStatus}
    <a href="{$row.$caseStatus.url}">{$row.$caseStatus.count}</a>
    {else}
     0
    {/if}
    </td>
   {/foreach}
  </tr>
  {/foreach}
</table>
{capture assign=findCasesURL}<a href="{crmURL p="civicrm/case/search" q="reset=1"}">{ts}Find Cases{/ts}</a>{/capture}

<span id='fileOnCaseStatusMsg' style="display:none;"></span><!-- Displays status from copy to case -->

<div class="spacer"></div>
    <h3>{if $myCases}{ts}My Cases With Upcoming Activities{/ts}{else}{ts}All Cases With Upcoming Activities{/ts}{/if}</h3>
    {if $upcomingCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="upcoming" rows=$upcomingCases}
    </div>
    {else}
        <div class="messages status">
	    {ts 1=$findCasesURL}There are no open cases with activities scheduled in the next two weeks. Use %1 to expand your search.{/ts}
        </div>
    {/if}

<div class="spacer"></div>
    <h3>{if $myCases}{ts}My Cases With Recently Performed Activities{/ts}{else}{ts}All Cases With Recently Performed Activities{/ts}{/if}</h3>
    {if $recentCases}
    <div class="form-item">
        {include file="CRM/Case/Page/DashboardSelector.tpl" context="dashboard" list="recent" rows=$recentCases}
    </div>
    {else}
        <div class="messages status">
	    {ts 1=$findCasesURL}There are no cases with activities scheduled in the past two weeks. Use %1 to expand your search.{/ts}
        </div>
    {/if}
{/if}
</div>