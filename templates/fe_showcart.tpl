{* List of products in cart *}

<script>
function UpdateQty(item)
{
 product_id = item.name.split("|")[0];
 attribute_id = item.name.split("|")[1];
 newQty = item.options[item.selectedIndex].text;
 document.location.href = 'index.php?mact=SimpleCart,cntnt01,cart,0&cntnt01product_id='+product_id+'&cntnt01attribute_id='+attribute_id+'&cntnt01qty='+newQty+'&cntnt01perfaction=update_product&returnmod=SimpleShop&cntnt01returnid='+{$returnid};
}
</script>
<div>{$continueshopping}</div>
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
			<th class="productprice">{$productpricetext}</th>
			<th class="productamount">{$lineamounttext}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$products item=entry}
			<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
				<td class="productqty"><select name={$entry->product_id}|{$entry->attribute_id} onchange="UpdateQty(this);">
							{html_options options=$entry->qtydropdown selected=$entry->myqty}
							</select>
				</td>
				<td class="productname">
				<a href="{$entry->prd_link}" title="{$entry->prd_name}" target="_blank">{$entry->prd_name}</a>
				{if $entry->attr_name != ''}<br />{$entry->attr_name}{/if}</td>
				<td class="productprice">{$entry->price}</td>
				<td class="productamount">{$entry->lineamount}</td>
				<td class="productremove">{$entry->deletelink}</td>
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
{/if}
</div>