{* Take the information for payment *}
{$startform}
{$welcometitle}<br>
{if isset($message)}
	<br>{$message}<br>
{/if}
<br>
<table id="paymentinfo" cellspacing="0" class="pagetable">
	<tr>
		<td id="shiplabel">{$paymentmethod_label}:</td>
		<td id="shipinput">{$paymentmethod_input}</td>
		<td id="shiperror">{if isset($paymentmethod_error)}{$paymentmethod_error}{/if}</td>
	</tr>
</table>
<p></p>
{$hidden}{$submit}{$cancel}{if isset($continue)}{$continue}{/if}<br>
{$endform}