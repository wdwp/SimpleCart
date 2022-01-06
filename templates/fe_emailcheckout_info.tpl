{* Take the information for shipping *}
{$startform}
{$welcometitle}<br />
{if isset($message)}
	<br>{$message}<br />
{/if}
<br />
{* if !$userloggedin}
<fieldset>
 <legend>{$title_fieldset_ec}</legend>
<br />
{cms_module module=FrontEndUsers form='login' only_groups="SimpleCart"}
</fieldset>
<br />
<fieldset>
 <legend>{$title_fieldset_nc}</legend>
 <br />
 {cms_module module=SelfRegistration group=SimpleCart}<br>
</fieldset>
<br />
{/if *}

<fieldset>
<legend>{$fieldsetshipto_label}</legend>
<table id="shipinfo" cellspacing="0" class="pagetable">
	<tr>
		<td class="shiplabel">{$firstname_label}:</td>
		<td class="shipinput">{$firstname_input}</td>
		<td class="shiperror">{if isset($firstname_error)}{$firstname_error}{/if}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$lastname_label}:</td>
		<td class="shipinput">{$lastname_input}</td>
		<td class="shiperror">{if isset($lastname_error)}{$lastname_error}{/if}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$email_label}:</td>
		<td class="shipinput">{$email_input}</td>
		<td class="shiperror">{if isset($email_error)}{$email_error}{/if}</td>
	</tr>
	{if $mandatorytelephone}
		<tr>
			<td class="shiplabel">{$telephone_label}:</td>
			<td class="shipinput">{$telephone_input}</td>
			<td class="shiperror">{if isset($telephone_error)}{$telephone_error}{/if}</td>
		</tr>
	{/if}
</table>
</fieldset>
<br />
<br />

<table id="deliveryinfo" cellspacing="0" class="pagetable">
	<tr>
		<td id="shiplabel">{$orderremark_label}:&nbsp;</td>
		<td id="shipinput">{$orderremark_input}</td>
		<td id="shiperror">&nbsp;</td>
	</tr>
	{if $SCouponsAvail}
	<tr>
		<td id="shiplabel">{$coupon_code_label}:</td>
		<td id="shipinput">{$coupon_code_input}</td>
		<td id="shiperror">{if isset($coupon_code_error)}{$coupon_code_error}{/if}</td>
	</tr>
	{/if}
	{if isset($paymentmethod_input)}
	<tr>
		<td id="shiplabel">{$paymentmethod_label}:&nbsp;</td>
		<td id="shipinput">{$paymentmethod_input}</td>
		<td id="shiperror">{if isset($paymentmethod_error)}{$paymentmethod_error}{/if}</td>
	</tr>
	{/if}
</table>
<br />
<br />
{$hidden}{$submit}{$cancel}

{$endform}