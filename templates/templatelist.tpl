
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$templatenametext}</th>			
			<th class="pageicon">{$defaultprompt}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			  
		</tr>
	</thead>
	<tbody>
	{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->name}</td>			
			<td align="center">{$entry->default}</td>
			<td>&nbsp;</td>
			<td>{$entry->editlink}</td>
			<td>{$entry->deletelink}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<div class="pageoptions">
	<p class="pageoptions">{$addlink}</p>
</div>
