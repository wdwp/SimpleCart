{* Register or Login as customer *}
{$startform}
{$welcometitle}
{if $error}
	{$error}<br>
{/if}
<div ID="login_new_customer"></div>
<fieldset>
	<legend>{$title_fieldset_nc}</legend>
		{$register}
</fieldset>
<div ID="login_customer">
<fieldset>
	<legend>{$title_fieldset_ec}</legend>
		<p>{$title_username}&nbsp;{$input_username}&nbsp;{$prompt_password}&nbsp;
		{$input_password}<br/>
		{if isset($captcha)}
			{$captcha_title}: {$input_captcha}<br />
			{$captcha}<br />
		{/if}
		{$login}<br />
		{$link_forgot}
</fieldset></div>
{$endform}