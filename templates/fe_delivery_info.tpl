{* Take the information for delivery    *}
{$startform}
{$welcometitle}<br>
{if isset($message)}
	<br>{$message}<br>
{/if}
<br>
<table id="deliveryinfo" cellspacing="0" class="pagetable">
	<tr>
		<td id="shiplabel">{$deliverymethod_label}:</td>
		<td id="shipinput">{$deliverymethod_input}</td>
		<td id="shiperror">{if isset($deliverymethod_error)}{$deliverymethod_error}{/if}</td>
	</tr>
	<tr>
		<td id="agreelabel">{if isset($agreetoterms_error) && $agreetoterms_error != ' '}{$agreetoterms_error}<br>{/if}
{$agreetoterms_label}:</td>
		<td id="agreeinput"><br>{$agreetoterms_input}</td>
	</tr>

</table>
<p></p>
{$hidden}{$submit}{$cancel}{if isset($continue)}{$continue}{/if}<br>
{$endform}