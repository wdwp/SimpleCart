{$startform}
	<fieldset>
	<legend>{$title_fieldset_amount}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_default_cartcurrency}:</p>
			<p class="pageinput">{$input_default_cartcurrency}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_numberformatdecimals}:</p>
			<p class="pageinput">{$input_numberformatdecimals}&nbsp;{$output_example}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_numberformatdec_point}:</p>
			<p class="pageinput">{$input_numberformatdec_point}&nbsp;{$output_parmexplanation}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_numberformatthousand_sep}:</p>
			<p class="pageinput">{$input_numberformatthousand_sep}</p>
		</div>
	</fieldset>
	<fieldset>
	<legend>{$title_fieldset_mandatory}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_mandatorystate}:</p>
			<p class="pageinput">{$input_mandatorystate}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_mandatorytelephone}:</p>
			<p class="pageinput">{$input_mandatorytelephone}</p>
		</div>
	</fieldset>
	<fieldset>
	<legend>{$title_fieldset_admincost}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_admincostadd}</p>
			<p class="pageinput">{$input_admincostadd}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_admincost}:</p>
			<p class="pageinput">{$input_admincost}&nbsp;{$title_admincostminamount}:&nbsp;
			<p class="pageinput">{$input_admincostminamount}</p>
		</div>
	</fieldset>
	<fieldset>
	<legend>{$title_fieldset_freeshipping}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_freeshippingboundary}</p>
			<p class="pageinput">{$input_freeshippingboundary}</p>
		</div>
	</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">{$title_contentthankyou}</p>
		<p class="pageinput">{$title_thankyouexplanation}<br>{$input_contentthankyou}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_contenttradeterms}</p>
		<p class="pageinput">{$title_tradetermsexplanation}<br>{$input_contenttradeterms}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_contenttradetext}</p>
		<p class="pageinput">{$input_contenttradetext}</p>
	</div>
	<fieldset>
	<legend>{$title_fieldset_vat}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_vatcode0}:</p>
			<p class="pageinput">{$input_vat0name}{$title_vatperc}&nbsp;{$input_vat0perc}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_vatcode1}:</p>
			<p class="pageinput">{$input_vat1name}{$title_vatperc}&nbsp;{$input_vat1perc}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_vatcode2}:</p>
			<p class="pageinput">{$input_vat2name}{$title_vatperc}&nbsp;{$input_vat2perc}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_vatcode3}:</p>
			<p class="pageinput">{$input_vat3name}{$title_vatperc}&nbsp;{$input_vat3perc}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_vatcode4}:</p>
			<p class="pageinput">{$input_vat4name}{$title_vatperc}&nbsp;{$input_vat4perc}</p>
		</div>
	</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">{$title_orderhandlingtype}</p>
		<p class="pageinput">{$input_orderhandlingtype}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_lastusedordernumber}</p>
		<p class="pageinput">{$input_lastusedordernumber}</p>
	</div>
	<fieldset>
	<legend>{$title_fieldset_invoice}</legend>
		<div class="pageoverflow">
			<p class="pagetext">{$title_invoice_prefix}:</p>
			<p class="pageinput">{$input_invoice_prefix}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_lastusedinvoicenumber}:</p>
			<p class="pageinput">{$input_lastusedinvoicenumber}</p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">{$title_extdoc_invoice}:</p>
			<p class="pageinput">{$input_extdoc_invoice}</p>
		</div>
	</fieldset>

	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
