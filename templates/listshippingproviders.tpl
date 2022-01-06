{if $itemcount > 0}
<table cellspacing="0" class="pagetable cms_sortable"">
	<thead>
		<tr>
			<th>{$shipcodetext}</th>
			<th>{$descriptiontext}</th>
			<th>{$shipprovpricetext}</th>
			<th data-sorter="false">{$mod->Lang('status_active')}</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
			<th data-sorter="false" class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->shipprovcode}</td>
			<td>{$entry->shipprovdesc}</td>
			<td>{$entry->shipprovprice}</td>
			{if $entry->status}<td>{$entry->status}</td>{/if}
			<td>{$entry->editlink}</td>
			<td>{$entry->deletelink}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
{/if}

<div class="pageoptions"><p class="pageoptions">{$addshipprovlink}</p></div>
