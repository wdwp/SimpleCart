{* Take the information for shipping *}
{$startform}
{$welcometitle}<br>
{if isset($message) && !empty($message)}
	<br>{$message}<br>
{/if}
<br>
{* if !$userloggedin}
<fieldset>
 <legend>{$title_fieldset_ec}</legend>
<br>
{cms_module module=FrontEndUsers form='login' only_groups="SimpleCart"}
</fieldset>
<br>
<fieldset>
 <legend>{$title_fieldset_nc}</legend>
 <br>
 {cms_module module=SelfRegistration group=SimpleCart}<br>
</fieldset>
<br>
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
	<tr>
		<td class="shiplabel">{$addressstreet_label}:</td>
		<td class="shipinput">{$addressstreet_input}</td>
		<td class="shiperror">{if isset($addressstreet_error)}{$addressstreet_error}{/if}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$addresscity_label}:</td>
		<td class="shipinput">{$addresscity_input}</td>
		<td class="shiperror">{if isset($addresscity_error)}{$addresscity_error}{/if}</td>
	</tr>
	{if isset($mandatorystate)}
		<tr>
			<td class="shiplabel">{$addressstate_label}:</td>
			<td class="shipinput">{$addressstate_input}</td>
			<td class="shiperror">{if isset($addressstate_error)}{$addressstate_error}{/if}</td>
		</tr>
	{/if}
	<tr>
		<td class="shiplabel">{$addresszip_label}:</td>
		<td class="shipinput">{$addresszip_input}</td>
		<td class="shiperror">{if isset($addresszip_error)}{$addresszip_error}{/if}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$addresscountry_label}:</td>
		<td class="shipinput">{$addresscountry_input}</td>
		<td class="shiperror">{if isset($addresscountry_error)}{$addresscountry_error}{/if}</td>
	</tr>
</table>
</fieldset>
<br />

<div onclick="toggle()" class="billtoinfo">{$billtoshow}</div>
<div class="billinfodtl" style="display:none;">
<fieldset>
<legend>{$fieldsetbillto_label}</legend>
<table cellspacing="0">
	<tr>
		<td class="shiplabel">{$billfirstname_label}:</td>
		<td class="shipinput">{$billfirstname_input}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$billlastname_label}:</td>
		<td class="shipinput">{$billlastname_input}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$billaddressstreet_label}:</td>
		<td class="shipinput">{$billaddressstreet_input}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$billaddresscity_label}:</td>
		<td class="shipinput">{$billaddresscity_input}</td>
	</tr>
	{if $mandatorystate}
		<tr>
			<td class="shiplabel">{$billaddressstate_label}:</td>
			<td class="shipinput">{$billaddressstate_input}</td>
		</tr>
	{/if}
	<tr>
		<td class="shiplabel">{$billaddresszip_label}:</td>
		<td class="shipinput">{$billaddresszip_input}</td>
	</tr>
	<tr>
		<td class="shiplabel">{$billaddresscountry_label}:</td>
		<td class="shipinput">{$billaddresscountry_input}</td>
	</tr>
</table>
</fieldset>
<br />
</div>
<br>

<table id="deliveryinfo" cellspacing="0" class="pagetable">
	<tr>
		<td class="shiplabel">{$orderremark_label}:&nbsp;</td>
		<td class="shipinput">{$orderremark_input}</td>
		<td class="shiperror">&nbsp;</td>
	</tr>
	{if $SCouponsAvail}
	<tr>
		<td class="shiplabel">{$coupon_code_label}:</td>
		<td class="shipinput">{$coupon_code_input}</td>
		<td class="shiperror">{if isset($coupon_code_error)}{$coupon_code_error}{/if}</td>
	</tr>
	{/if}
	<tr>
		<td class="shiplabel">&nbsp;</td>
		<td class="shipinput">&nbsp;</td>
		<td class="shiperror">&nbsp;</td>
	</tr>
	<tr>
		<td class="shiplabel">{$deliverymethod_label}:</td>
		<td class="shipinput">{$deliverymethod_input}</td>
		<td class="shiperror">{if isset($deliverymethod_error)}{$deliverymethod_error}{/if}</td>
	</tr>
	<tr>
		<td id="agreelabel">{if isset($agreetoterms_error) && $agreetoterms_error != ' '}{$agreetoterms_error}<br>{/if}
{$agreetoterms_label}:</td>
		<td id="agreeinput"><br>{$agreetoterms_input}&nbsp;{if isset($agreeterms_error)}<span class="shiperror">{$agreeterms_error}</span>{/if}</td>
	</tr>
	<tr>
		<td class="shiplabel">&nbsp;</td>
		<td class="shipinput">&nbsp;</td>
		<td class="shiperror">&nbsp;</td>
	</tr>
	{if isset($paymentmethod_input)}
	<tr>
		<td class="shiplabel">{$paymentmethod_label}:&nbsp;</td>
		<td class="shipinput">{$paymentmethod_input}</td>
		<td class="shiperror">{if isset($paymentmethod_error)}{$paymentmethod_error}{/if}</td>
	</tr>
	{/if}
</table>
<br />
<br />
{$hidden}{$submit}{$cancel}

{$endform}

<script>
function toggle() {
  var x = document.getElementsByClassName("billinfodtl")[0];
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
