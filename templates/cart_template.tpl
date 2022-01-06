{* List of products in cart *}
{literal}
<script language="JavaScript">

function UpdateQty(item)
{
	product_id = item.name;
	newQty = item.options[item.selectedIndex].text;
	<!-- I know, the following is not very neatly done, but it works. Sorry :-) // -->
	document.location.href = 'index.php?mact=SimpleCart,cntnt01,cart,0&cntnt01product_id='+product_id+'&cntnt01qty='+newQty+'&cntnt01perfaction=update_product';
}

</script>
{/literal}
<div class="productlist">
{if $productcount != 0}
	<div ID="productcount">{$label_product_count}</div>
{/if}
<table>
	{if $productcount > 0}
	<thead>
		<tr>
			<th>{$productqtytext}</th>
			<th>{$productnametext}</th>
			<th>{$productpricetext}</th>
			<th>{$lineamounttext}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$products item=entry}
			<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
				<td class="productqty"><select name={$entry->product_id} onchange="UpdateQty(this);"> 
							{html_options options=$entry->qtydropdown selected=$entry->myqty}
							</select></td>
				<td class="productname">{$entry->name}</td>
				<td class="productprice">{$entry->price}</td>
				<td class="productamount">{$entry->lineamount}</td>
				<td class="productremove">{$entry->deletelink}</td>
				<td class="productid">{$entry->product_id}</td>
			</tr>
		{/foreach}
	{else}
		<tr class="{cycle values="row1,row2"}">
			<td colspan='5' align='center'>{$noproductsincart}</td>
		</tr>
	</tbody>
	{/if}
</table>
{if $productcount > 0}
	<div ID="totalamount">{$label_total_amount}</div>
	<div ID="checkout">{$startcheckout}</div>
	<div ID="contshopping">{$continueshopping}</div>
{/if}
</div>