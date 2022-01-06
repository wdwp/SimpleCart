{* Front End: Products with names *}
{if isset($productcount) && $productcount > 0}
<table>
	{foreach from=$products item=entry}
		<tr>
			<td class="productname">{$entry->prd_name}</td>
		</tr>
	{/foreach}
</table>
{/if}
<div class="cartproducts">
	{$label_product_count}
</div>
{if isset($viewcart)}
<div class="cartview">
	{if isset($viewcart)}{$viewcart}{/if}
</div>
{/if}