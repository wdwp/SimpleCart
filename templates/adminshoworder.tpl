{* Show the details of an order *}
{$startform}
{if isset($submit)}{$submit}{/if}{$cancel}
<fieldset>
<legend>{$welcometitle}</legend>
{if isset($message)}
	<br />{$message}<br />
{/if}
<div>{$ordernumber_label}:&nbsp;{$ordernumber}</div>
<div>{$orderdate_label}:&nbsp;{$orderdate}</div>
</fieldset>
<fieldset>
<legend>{$mod->Lang('contactinfo')}</legend>
{$shiptotelephone}<br />
{$shiptoemail}
</fieldset>
{if $orderhandlingtype == 'normal' || $orderhandlingtype == 'speed' || $orderhandlingtype == 'email'}

<fieldset>
<legend>{$mod->Lang('ship_to')}</legend>
<div>
<table class="">
	<thead>
		<tr>
			<th>{$shipto_label}</th>
			<th>&nbsp;|&nbsp;</th>
			<th>{$billto_label}</th>
		</tr>
	</thead>
	<tbody>
		<tr >
			<td >{$shiptoname}<br />{$shiptostreet}{$shiptocity}{$shiptozip}{$shiptostate}{$shiptocountry}</td>
			<td >&nbsp;|&nbsp;<br />&nbsp;|&nbsp;<br />&nbsp;|&nbsp;<br />&nbsp;|&nbsp;<br />&nbsp;|&nbsp;<br />&nbsp;|&nbsp;</td>
			<td >{$billtoname}<br />{$billtostreet}{$billtocity}{$billtozip}{$billtostate}{$billtocountry}</td>
		</tr>
	</tbody>
</table>
</div>
<br>
<div class="totalnetweight">
	{$label_total_weight}
</div>
</fieldset>

{/if}
<br />
<div class="productlist">
<table class="pagetable">
	<thead>
		<tr>
			<th>{$productqtytext}</th>
			<th>{$productidtext}</th>
			<th>{$productitemnumbertext}</th>
			<th>{$productskutext}</th>
			<th>{$productnametext}</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>{$productpricetext}</th>
			<th>{$lineamounttext}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$products item=entry name=foo}
			<tr class="row{cycle values="1,2"}" >
				<td>{$entry->qty}</td>
				<td>{$entry->product_id}</td>
				<td>{$entry->itemnumber}</td>
				<td>{$entry->sku}</td>
				<td>{$entry->categoryname}</td>
				<td>{$entry->productname}</td>
				<td>{$entry->attributename}</td>
				<td>{$entry->price}</td>
				<td>{$entry->lineamount}</td>
			</tr>
		{/foreach}
		{if isset($discount_amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="discountname"><br>{$label_totaldiscount}</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$discount_amount}</td>
		</tr>
		{/if}
		{if isset($admin_amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="admincostname"><br />{$label_admin_amount}</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$admin_amount}</td>
		</tr>
		{/if}
		{if isset($totalvat0amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="vatname"><br />{$label_vat0_amount}%</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$totalvat0amount}</td>
		</tr>
		{/if}
		{if isset($totalvat1amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="vatname"><br />{$label_vat1_amount}%</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$totalvat1amount}</td>
		</tr>
		{/if}
		{if isset($totalvat2amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="vatname"><br />{$label_vat2_amount}%</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$totalvat2amount}</td>
		</tr>
		{/if}
		{if isset($totalvat3amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="vatname"><br />{$label_vat3_amount}%</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$totalvat3amount}</td>
		</tr>
		{/if}
		{if isset($totalvat4amount)}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td class="vatname"><br />{$label_vat4_amount}%</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$totalvat4amount}</td>
		</tr>
		{/if}
		{if $deliveryprice <> 0}
		<tr >
			<td colspan="6">&nbsp;</td>
			<td ><br />{$deliveryvia}</td>
			<td >&nbsp;</td>
			<td style="text-align:right"><br />{$deliveryprice}</td>
		</tr>
		{/if}
		<tr >
			<td colspan="4">&nbsp;</td>
			<td class="productqty"><br />&nbsp;</td>
			<td >&nbsp;</td>
			<td class="productname">{$label_total_amount}</td>
			<td class="productprice">{$currency}</td>
			<td style="text-align:right">{$total_amount}</td>
		</tr>
	</tbody>
</table>
{if $orderhandlingtype == 'normal' || $orderhandlingtype == 'speed' || $orderhandlingtype == 'email'}
<fieldset>
<legend>{$mod->Lang('delivery')}</legend>
{if $deliveryprice == 0}
	<div>{$deliveryvia_label}{$deliveryvia}</div>
	<br />
{/if}
<div>{$deliverydate_label}<strong>{$deliverydate}</strong>&nbsp;{$deliverydateexpl}</div>
</fieldset>
{/if}
<fieldset>
<legend>{$mod->Lang('paymentmethod_label')}</legend>
<div>{$paymentvia_label}{$paymentvia}</div>
</fieldset>
<fieldset>
<legend>{$orderremark_label}:</legend>
<div>{$orderremark}</div>
</fieldset>
<br>
{$hidden}{$hiddenstatus}{if isset($submit)}{$submit}{/if}{$cancel}
{$endform}
<br>