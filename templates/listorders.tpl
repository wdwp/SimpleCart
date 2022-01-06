{* Show all orders. Orders are selected using link with status *}
{literal}
<script type="text/javascript">
function selectall() {
	checkboxes = document.getElementsByTagName("input");
	for (i=0; i<checkboxes.length ; i++) {
	  if (checkboxes[i].type == "checkbox") checkboxes[i].checked=!checkboxes[i].checked;
	}
}
</script>
{/literal}
{$form2start}
<div ID="SelectOrders">
	{$selectINTorderslink}&nbsp;
	{$selectCNForderslink}&nbsp;
	{$selectPAYorderslink}&nbsp;
	{$selectSHPorderslink}&nbsp;
	{$selectINVorderslink}&nbsp;
	<span style="font-weight:bold;">{$title_ordercount}<span style="font-size:large;">{$ordercount}</span></span>
</div>
<div></div>
{if $ordercount > 0}
	{if $ordercount > 20}
		{if $orderstatus == 'INT' || $orderstatus == 'INV'}
			{$submit_massdelete}<br>
		{/if}
	{/if}
<table cellspacing="0" class="pagetable cms_sortable">
	<thead>
		<tr>
			<th>{$ordertext}</th>
			<th>{$shipmodetext}</th>
			<th>{$customernametext}</th>
			<th>{$moddatetext}</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
			{if $orderstatus == 'INV'}
				<th data-sorter="false" class="pageicon">&nbsp;</th>
				<th data-sorter="false" class="pageicon">&nbsp;</th>
				<th data-sorter="false" class="pageicon">&nbsp;</th>
			{/if}
			<th data-sorter="false" class="pageicon">&nbsp;</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
			<th data-sorter="false" class="pageicon">{$inputselectall}</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$orders item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->order_id}</td>
			<td>{$entry->shipmode}</td>
			<td>{$entry->customername}</td>
			<td>{$entry->modificationdate}</td>
			<td>{$entry->viewlink}</td>
			{if $orderstatus == 'INV'}
				<td>{$entry->extdocsend}</td>
				<td>{$entry->extdocpreview}</td>
				<td>{$entry->extdocprep}</td>
			{/if}
			<td>{if isset($entry->switchlink)}{$entry->switchlink}{/if}</td>
			<td>{if isset($entry->deletelink)}{$entry->deletelink}{/if}</td>
			<td>{$entry->checked}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
<p style="margin-top: -2px; float: right; text-align: right">
	{$title_withselected}&nbsp;
	{$input_withselected}&nbsp;
	{$submit}
</p>
<br />
<br />
{/if}
{$form2end}