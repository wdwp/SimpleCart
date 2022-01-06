{if isset($message)}
	<div class="pagemcontainer">
		<p class="pagemessage">{$message}</p>
	</div>
{/if}
{$formstart}
<div class="pageoverflow">
  <p class="pagetext">{$prompt_templatename}:</p>
  <p class="pageinput">{$templatename}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_template}:</p>
  <p class="pageoptions">{$template}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageoptions">{$submit}{$cancel}{$apply}</p>
</div>
{$hidden}
{$formend}
