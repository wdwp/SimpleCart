{* Confirmation of the order *}
{$startform}
{$welcometitle}<br />
{if isset($message)}
	<br />{$message}<br />
{/if}
<br />
{if $orderhandlingtype == 'normal' || $orderhandlingtype == 'speed'}
<div class="shiptoaddress">{$shipto_label}<br>
{$shiptoname}
{$shiptostreet}
{$shiptocity}
{$shiptostate}
{$shiptozip}
{$shiptocountry}
{$shiptotelephone}
</div>
<div class="billtoaddress">{$billto_label}<br />
{$billtoname}
{$billtostreet}
{$billtocity}
{$billtostate}
{$billtozip}
{$billtocountry}
</div>
<br />
<div class="totalnetweight">
	{$label_total_weight}
</div>
{/if}
{if $orderhandlingtype == 'email'}
{$shiptoname}
{$email}
{/if}
<br /><br />
<div class="productlist">
<table>
	<thead>
		<tr>
			<th>{$productqtytext}</th>
			<th>{$productnametext}</th>
			<th>{$productpricetext}</th>
			<th>{$lineamounttext}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$products item=entry}
			<tr >
				<td class="productqty">{$entry->qty}</td>
				<td class="productname">{$entry->productname} {if $entry->attributename != ''}<br />{$entry->attributename}{/if}</td>
				<td class="productprice">{$entry->price}</td>
				<td class="productamount">{$entry->lineamount}</td>
			</tr>
		{/foreach}
		{if isset($discount_amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="discountname"><br>{$label_totaldiscount}</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$discount_amount}</td>
		</tr>
		{/if}
		{if isset($admin_amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="admincostname"><br>{$label_admin_amount}</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$admin_amount}</td>
		</tr>
		{/if}
		{if isset($totalvat0amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="vatname"><br>{$label_vat0_amount}%</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$totalvat0amount}</td>
		</tr>
		{/if}
		{if isset($totalvat1amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="vatname"><br>{$label_vat1_amount}%</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$totalvat1amount}</td>
		</tr>
		{/if}
		{if isset($totalvat2amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="vatname"><br>{$label_vat2_amount}%</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$totalvat2amount}</td>
		</tr>
		{/if}
		{if isset($totalvat3amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="vatname"><br>{$label_vat3_amount}%</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$totalvat3amount}</td>
		</tr>
		{/if}
		{if isset($totalvat4amount)}
		<tr >
			<td >&nbsp;</td>
			<td class="vatname"><br>{$label_vat4_amount}%</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$totalvat4amount}</td>
		</tr>
		{/if}
		{if $deliveryprice <> 0}
		<tr >
			<td >&nbsp;</td>
			<td ><br>{$deliveryvia}</td>
			<td >&nbsp;</td>
			<td class="productamount"><br>{$deliveryprice}</td>
		</tr>
		{/if}
		<tr >
			<td class="productqty"><br>&nbsp;</td>
			<td class="productname">{$label_total_amount}</td>
			<td class="productprice">&nbsp;</td>
			<td class="productamount">{$total_amount}</td>
		</tr>
	</tbody>
</table>
</div>
{if $orderhandlingtype != 'email'}
 {if $deliveryprice == 0}
	<div>{$deliveryvia_label}{$deliveryvia}</div>
	<br />
 {/if}
{/if}
<div>{$paymentvia_label}{$paymentvia}</div>
<br />
{if $orderhandlingtype != 'email'}
<div>{$deliverydate_label}{$deliverydate}</div>
<br />
{/if}
{$hidden}{$confirm}{$cancel}<br>
{$endform}