<p>
Dear {$shiptoname}<br />
Please find below the details of the order that you just placed.<br />
Order: {$order_id}<br />
Customer number: {$customer_id}<br />
{if isset($newcustomer)}{$title_name}: {$username}<br>{$title_password}: {$password}<br>{/if}

The goods will be sent to:<br />
{if isset($shiptostreet) && !empty($shiptostreet)}{$shiptostreet}{/if}
{if isset($shiptocity) && !empty($shiptocity)}{$shiptocity}{/if}
{if isset($shiptostate) && $shiptostate != ''}{$shiptostate}{/if}
{if isset($shiptozip) && $shiptozip != ''}{$shiptozip}{/if}
{if isset($shiptocountry) && !empty($shiptocountry)}{$shiptocountry}{/if}<br /><br />

The bill will be sent to:<br />
{if isset($billtostreet) && !empty($billtostreet)}{$billtostreet}{/if}
{if isset($billtocity) && !empty($billtocity)}{$billtocity}{/if}
{if isset($billtostate) && $billtostate != ''}{$billtostate}{/if}
{if isset($billtozip) && $billtozip != ''}{$billtozip}{/if}
{if isset($billtocountry) && !empty($billtocountry)}{$billtocountry}{/if}<br /><br />
<table border="0">
    <thead>
        <tr>
            <td>Qty</td>
            <td>Name</td>
            <td>Price</td>
            <td>Total</td>
        </tr>
    </thead>
    <tbody>
     {foreach  from=$products item=entry}
        <tr>
            <td>{$entry->qty}</td>
            <td>
            {if isset($entry->productname)}{$entry->productname}{/if}
            {if isset($entry->attributename)}{$entry->attributename}{/if}
            </td>
            <td>{$entry->price}</td>
            <td>{$entry->lineamount}</td>
        </tr>
     {/foreach}
      {if $productcount > 1}
        <tr>
            <td>&nbsp;</td>
            <td>Total products</td>
            <td>&nbsp;</td>
            <td>{$totalproduct}</td>
        </tr>
      {/if}
      {if $totaldiscount > 0}
        <tr>
            <td>&nbsp;</td>
            <td>Total discount</td>
            <td>&nbsp;</td>
            <td>{$totaldiscount}</td>
        </tr>
      {/if}
      {if $totalshipping > 0}
        <tr>
            <td>&nbsp;</td>
            <td>Shipping</td>
            <td>&nbsp;</td>
            <td>{$totalshipping}</td>
        </tr>
      {/if}
      {if $totalvatamount > 0}
        <tr>
            <td>&nbsp;</td>
            <td>Vat</td>
            <td>&nbsp;</td>
            <td>{$totalvatamount}</td>
        </tr>
      {/if}
      {if $totaladmincost > 0}
        <tr>
            <td>&nbsp;</td>
            <td>Administration costs</td>
            <td>&nbsp;</td>
            <td>{$totaladmincost}</td>
        </tr>
      {/if}
    </tbody>
</table>
<br />
Total amount: ({$total_amount})<br />
<br />
{if isset($remark)}{$remark}{/if}
<br />
Pending payments you may expect the goods by {$deliverydate}.
<br />
<br />
Thank you very much for your order!<br />
<br />
<br />
If you have received this mail without placing an order, we are so sorry that someone else has used your email address.
</p>