<ul>
    <li>Version 1.0 December 2022</li>
	<ul>
	<li>Rewritten for cmsms 2.x</li>
	</ul>
	<li>Version 0.4.4 - 23 March 2013<br />
		<ul>
			<li>Solves problem with continue button on front end (bug# 8827);</li>
			<li>New variables umweight and totalnetweight available on order delivery template (Req# 8533).</li>
		</ul>
	</li>
	<li>Version 0.4.3 - 4 December 2012<br />
		<ul>
			<li>Many internal changes to comply with lazy loading of module parts;</li>
			<li>Icon set to easy select type of orders on Orders tab;</li>
			<li>Update connect to Payment Made Simple new style;</li> 
			<li>Minor change in confirmation template (closing div was missing).</li>
		</ul>
	</li>
	<li>Version 0.4.2 - 30 January 2012<br />
		<ul>
			<li>New option to set free shipping amount boundary;</li>
			<li>Recalculate expected delivery date after payment is received was not working;</li>
		</ul>
	</li>
	<li>Version 0.4.1 - 15 January 2012<br />
		<ul>
			<li>Added classes shiptoaddress and billtoaddress to default css and order confirm template;</li>
			<li>Minor change on default font to use in mails;</li>
			<li>It was not possible to add new templates;</li>
			<li>Message after deletion of a template was wrong;</li>
			<li>Default template does not have deletion icon;</li>
			<li>CartMS default stylesheet was not installed automatically;</li>
			<li>Checkmarks on mail settings tab not saved correctly in 1.10 environment;</li>
			<li>Checkmarks on options tab not saved correctly in 1.10 environment.</li>
		</ul>
	</li>
	<li>Version 0.4.0 - 12 October 2011<br />
		<ul>
			<li>Upgraded security on the options tab (it was not set);</li>
			<li>Include buttons for check out and continue shopping (<a href="http://dev.cmsmadesimple.org/feature_request/view/6753" target="_blank">FeatReq# 6753</a>)<br />
			Use the following lines in showcart template:<br />
			{$btn_checkout_startform}{$btn_checkout}{$btn_checkout_endform}<br />
{$btn_continue_startform}{$btn_continueshopping}{$btn_continue_endform}<br />
			Button images are checkout.png and continue.png (prepared via http://cooltext.com/Cloud-Button) 
			which are located in the modules\SimpleCart\images folder.</li>
			<li>Template speedcheckout_info changed since used start/stopExpandCollapse is not supported as of 1.10;</li>
			<li>Moved tabs in back end arround to have them in more standard sequence;</li>
			<li>Version prepared for use as of CMSMS vs 1.9.4.x and 1.10.</li>
		</ul>
	</li>
	<li>Version 0.3.9 - July 29 2011<br />
		<ul>
			<li>Connection to module Discount Coupons build in;</li>
			<li>Update template fe_shipping_info template with:<br />
				{if $SCouponsAvail}<br />
					&nbsp;&nbsp;&lt;div>{$coupon_code_label}:&lt;/div><br />
					&nbsp;&nbsp;&lt;div>{$coupon_code_input}&lt;/div><br />
				{/if}<br />
				This to show the coupon code entry field in for the customer.<br />
				Update fe_speedcheckout_info template with:<br />
				{if $SCouponsAvail}<br />
				&lt;tr><br />
					&nbsp;&nbsp;&lt;td id="shiplabel">{$coupon_code_label}:&lt;/td><br />
					&nbsp;&nbsp;&lt;td id="shipinput">{$coupon_code_input}&lt;/td><br />
					&nbsp;&nbsp;&lt;td id="shiperror">{$coupon_code_error}&lt;/td><br />
				&lt;/tr><br />
				{/if}<br />
				Use the following in the fe_orderconfirm template:<br />
				{if $discount_amount}<br />
				&lt;tr ><br />
					&nbsp;&nbsp;&lt;td >&nbsp;&lt;/td><br />
					&nbsp;&nbsp;&lt;td class="discountname">&lt;br>{$label_totaldiscount}&lt;/td><br />
					&nbsp;&nbsp;&lt;td >&nbsp;&lt;/td><br />
					&nbsp;&nbsp;&lt;td class="productamount">&lt;br>{$discount_amount}&lt;/td><br />
				&lt;/tr><br />
				{/if}<br />

			</li>
			<li>Minor changes to make sure Postgress can be used as well (thanks go to Brett of Binarystudios.net)</li>
		</ul>
	</li>
	<li>Version 0.3.8 - June 14 2011<br />
		<ul>
			<li>VAT was not calculated correctly when products were sold, attribute selling went ok;</li>
			<li>All orders (no check on status) can be mass updated to other status or deleted (<a href="http://dev.cmsmadesimple.org/feature_request/view/5644" target="_blank">FeatReq# 5644</a>);</li>
			<li>Administration, shipping and VAT amount were not printed on pdf invoice.</li>
		</ul>
	</li>
	<li>Version 0.3.7 - June 12 2011<br />
		<ul>
			<li>If Inventory Manager module is installed, inventory transaction is raised on sale;</li>
			<li>Total price was not calculated correctly after upgrade from previous version 0.2.8;</li>
			<li>Dependent on the setting in ShopMS, the inventory is decreased at correct timing (after 
				order is confirmed, paid, shipped or invoiced);</li>
			<li>Changed the maximum version of CMSMS to make sure no problems with version 1.10 of CMSMS.</li>
		</ul>
	</li>
	<li>Version 0.3.6 - April 24 2011<br />
		<ul>
			<li>Delivery terms were not correctly handled on Order speed checkout (extend the fe_speedcheckout_info by adding 
				{$agreeterms_error} after {$agreetoterms_input} to see an error message);</li>
			<li>Fixed bug# 6307, that caused program orderspeedsave to stop the process (solution by adikat);</li>
			<li>If product was selected that was in root category, it was not shown in cart.</li>
		</ul>
	</li>
	<li>Version 0.3.5 - December 14 2010<br />
		<ul>
			<li>Pseudocron task made available that removes not followed up orders.</li>
		</ul>
	</li>
	<li>Version 0.3.4 - November 21 2010<br />
		<ul>
			<li>Pyament method iDEAL Pro not picked up correctly;</li>
			<li>Incorrectly showing payment code in stead of description on confirm page.</li>
		</ul>
	</li>
	<li>Version 0.3.3 - November 21 2010<br />
		<ul>
			<li>New template added that will be used, when an invoice is send to the customer;</li>
			<li>Removed internal usage function eregi, since this function has been deprecated as of PHP 5.3.0;</li>
			<li>Since version 0.3.1 the orders were not shown in the backend. Solved with this version.</li>
		</ul>
	</li>
	<li>Version 0.3.2 - November 18 2010<br />
		<ul>
			<li>The PHP warnings were removed in previous version, but testing revealed problem during confirm order. Lines
			were not shown anymore. Solved with this version.</li>
		</ul>
	</li>
	<li>Version 0.3.1 - November 17 2010<br />
		<ul>
			<li>A check is now build in towards ShopMS to see if quantity on stock. On front end 'sold out' is shown if no more stock;</li>
			<li>Message that appears if PHP warning are to be shown 'Call-time pass-by-reference has been deprecated' has been solved;</li>
			<li>Prepare default value for invoice to be generated in options tab;</li>
		</ul>
	</li>
	<li>Version 0.3.0 - November 6 2010<br />
		<ul>
			<li>New options related to invoice numbering in place in the options tab;</li>
			<li>Preview build in for invoices. New PDF logo that shows invoice;</li>
			<li>During installation directory uploads/SimpleCart is prepared to hold the generated invoices;</li>
			<li>New order check out type introduced: email. This allows that, for example, event tickets can be sold, prepared and send.
			This opens up the possibility to sell digital goods in future;</li>
			<li>Minimum version set to 1.8.1 due to removal public member variables;</li>
			<li>Attributes of product are now part of an order;</li>
			<li>During installation a default template is set to show the cart;</li>
			<li>Apply button added to template maintenance;</li>
			<li>Four variables made available to show in the cart: {$category_name}, {$category_description}, 
			{$total_amount} and {$currency};</li>
			<li>Due to introduction of new variables on the confirm page, please change, in template fe_orderconfirm, 
			{$entry->name} into {$entry->productname} after upgrading;</li>
			<li>New variables in fe_orderconfirm template: {$entry->categoryname} and {$entry->attributename};</li>
			<li>During fresh install, FEU new user properties are not set anymore as to be filled in on 'Ask in lost name' form</li>
			<li>Moved functions internally around, so module becomes more like standard;</li>
			<li>Get rid of public member variables such as 'global $gCms';</li>
			<li>Version 0.2.9 on purpose skipped, since this is a major release. Since this 
			version breaks with the original, Duketown thanks Gary Greyling (Lahuindai) 
			for all his perfect work. Time to move on.</li>
		</ul>
	</li>
	<li>Version 0.2.8 - September 11 2010<br>
		<ul>
			<li>Make it possible to install module without class Orders to cause problems;</li>
			<li>Last used order number can be set to new value (<a href="http://dev.cmsmadesimple.org/feature_request/view/4675" target="_blank">FeatReq# 4675</a>);</li>
			<li>Allow module to run in 1.8 environment of CMSMS;</li>
			<li>Extended help text with regards to how visitor/customer is handled in FEU;</li>
			<li>Password was not send to new customers. This was due to missing table module_cartms_newuserpasswords.<br />
			This problem exists as of version 0.2.3 for new installs. Upgrade from before don't have that problem.</li>
		</ul>
	</li>
	<li>Version 0.2.7 - January 7 2010<br>
		<ul>
			<li>Debug statement left in coding that resulted in not functioning of speed check out.</li>
		</ul>
	</li>
	<li>Version 0.2.6 - January 4 2010<br>
		<ul>
			<li>New option available to set 'fast customer check out'. If set to 'speed', one check out page else three!</li>
			<li>New template 'fe_speedcheckout_info' to support new order handling type.<br />
			Make sure to include: .billtoinfo {display: none;} in a, to the page connected, stylesheet;</li>
			<li>The example style sheet available in css directory has been extended;</li>
			<li>If one payment method, use it and continue to confirmation without selection of payment by customer;</li>
			<li>Save mandatory telephone setting in options;</li>
			<li>Bill to name (both first and lastname) now available, both in back end and on front end;</li>
			<li>Bill to name made available in Front End Usermanagement module as properties;</li>
			<li>Update template fe_shipping_info with:<br />
				&lt;tr><br />
					&nbsp;&nbsp;&lt;td class="shiplabel">{$billfirstname_label}:&lt;/td><br />
					&nbsp;&nbsp;&lt;td class="shipinput">{$billfirstname_input}&lt;/td><br />
				&lt;/tr><br />
				&lt;tr><br />
					&nbsp;&nbsp;&lt;td class="shiplabel">{$billlastname_label}:&lt;/td><br />
					&nbsp;&nbsp;&lt;td class="shipinput">{$billlastname_input}&lt;/td><br />
				&lt;/tr><br />
				by inserting the above before the billaddressstreet part. 
			</li>
			<li>Update template fe_orderconfirm with:<br />
			 {$billtoname}<br />
			 by inserting the above before the billtostreet.
			</li>
		</ul>
	</li>
	<li>Version 0.2.5 - October 23 2009<br>
		<ul>
			<li>No amount formating problem towards PayPal if non english language on front end used;</li>
			<li>Allow PayPal payments to contain details per item;</li>
			<li>Check if Payment Made Simple is at correct level (at least 1.0.5) when installed and PayPal is a gateway (especially if it is active).</li>
		</ul>
	</li>
	<li>Version 0.2.4 - October 5 2009<br>
		<ul>
			<li>Price per wight included on delivery codes. Total weight of products is calculated and added to standard price of delivery;</li>
			<li>Remark per order can be entered by customer. Use {$orderremark_label}:&lt;br>{$orderremark_input}&lt;br> in the fe_shipping_info template;</li>
			<li>Remark from customer used in back end on detail and in confirmation mail: use {$remark} as variable);</li>
			<li>Cart follows settings of username is email and random username as set in FEU;</li>
			<li>Mail to customer also contains username next to password for new customers \'{$username}\';</li>
			<li>Removed tab User Authorization Templates since that is handled in FEU;</li>
			<li>Removed tab Preferences since Cart MS belongs in Extensions menu;</li>
			<li>Don't show internal product id in default installed show cart template;</li>
			<li>Amended template on shipment info.</li>
		</ul>
	</li>
	<li>Version 0.2.3 - May 05 2009<br>
		<ul>
			<li>Initialized variables correctly, so no warning appears for them when php.ini states to show warnings;</li>
			<li>Included generated password for new customer in order confirmation mail to customer (the original template);</li>
			<li>Change to default \'ship to\' template to correct fieldset used on ship to address.</li>
		</ul>
	<li>Version 0.2.2 - March 16 2009<br>
		<ul>
			<li>Delivery information will now react upon mandatory agree to terms situation;</li>
			<li>New text in option that is used for hyperlink to trading terms page;</li>
			<li>Changes made to solve typos on label in stead of lable (bug: 3102);</li>
			<li>Default template set during installation (bug: 3102);</li>
			<li>No longer usage of id but of class in newly installed template fe_shipping_info (bug: 3102);</li>
			<li>No more funny setting of fieldset on fe_shipping_info when not logged in (bug: 3102).</li>
		</ul> 
	</li>
	<li>Version 0.2.1 - February 17 2009<br>
		<ul>
			<li>Connection to Payment Made Simple is now gateway independent;</li>
			<li>Customer mailing template now includes expected delivery date (after installation);</li>
			<li>Minor cleaning of coding in order payment handling.</li>
		</ul>
	</li>
	<li>Version 0.2.0 - February 8 2009<br>
		<ul>
			<li>Expected delivery date is now available for the fe_orderconfirm template and the mail to customer template. Use:<br>
			\'&lt;div>{$deliverydate_label}{$deliverydate|date_format:$defaultdateformat}&lt;/div>&lt;br />\'<br>
			to show it on the order confirmation page;</li>
			<li>Amounts in inquiry detail order are now right aligned;</li>
			<li>Per delivery method a number of working days can be entered. These days will be used to calculate expected delivery date.
			During upgrade, the allready existing orders get the creation date as expected delivery date;</li>
			<li>Expected delivery date is shown in the back end, when one requests order detail;</li>
			<li>At the moment that order status changes from confirmed to paid, the expected delivery date is calculated based upon working 
			days as set up for delivery method;</li>
			<li>Per delivery method it is now possible, to make it mandatory, to state that the customer has to agree to trading terms;</li>
			<li>New admin option: page containing trading terms. Parameter will be used in checkout delivery terms selection step;</il>
			<li>Mail settings in back end promoted to new tab, so the options tab is less loaded/confusing.</li>
		</ul>
	</li>
	<li>Version 0.1.9 - February 3 2009<br>
		<ul>
			<li>Mail was not send during confirmation or had no information.</il>
		</ul>
	</li>
	<li>Version 0.1.8 - February 2 2009<br>
		<ul>
			<li>Extended support for payment gateways. Each gateway needs its own settings, so split is needed here;</li>
			<li>Serious bug discovered and temporary solved: orders where paid, but status not set to at least confirmed. At this
			moment if the customer presses confirm, order will get status confirmed, mail will be send and only then payment
			handling will be done (this used to be payment handling, and based upon payment result, set the status of the order).</li>
		</ul>
	</li>
	<li>Version 0.1.7 - January 29th 2009<br>
		<ul>
			<li>Ship info template that is loaded during installation was not working properly;</li>
			<li>VAT was calculated even when this should not be done according to ShopMS (bug #2741). This has been fixed;</li>
			<li>If order was \'promoted\' to next status, the next page was always with confirmation orders. Now same page rebuild;</li>
			<li>Length of subject of mail to order handler extended from 50 to 68 positions (is max due to CMSMailer module);</li>
			<li>Values \'{$order_id}\', \'{$shiptoname}\' and \'{$customer_id}\' can now be used in the subject of the mail to order
			handler. These parameters will be replaced by their values.</li>
		</ul>
	</li>
	<li>Version 0.1.6 - December 21st 2008<br>
		<ul>
			<li>Email address available for the confirmation mail to the customer (so can be used in the template {$email});</li>
			<li>Solved bug on installation of properties for FEU;</li>
			<li>When new customer, a password is now generated. If customer created in last 8 hours, the customer is known as new.<br>
			You are able to use  {if $newcustomer}{$title_password}: {$password}{/if} in the confirmation mail that you prepare in the options.</li>
		</ul>
	</li>
	<li>Version 0.1.5 - December 16th 2008<br>
		<ul>
			<li>Now it is possible to handle returning customers! Use module FrontEndUsers to have the user login. Previous address information will be retrieved from 
			the properties of the customer;</li>
			<li>Bill to address now available in this module.<br>
			Variables in fe_confirm_info are: $billtostreet; $billtocity; $billtostate; $billtozip and $billtocountry.<br>
			Variables in fe_shipping_info are: $billaddressstreet_input; $billaddressstreet_input; $billaddressstate_input; $billaddresszip_input; and $billaddresscountry_input.<br>
			Labels in fe_ship_info are the same as the variables, but _input should be changed into _label.<br>
			Make sure to add the countries available for property addresscountry are also available in property billtocountry.<br>
			No validation is done on the bill to address on the front end, since the ship to is validated. When a bill to field is left blank, it will be filled
			with the ship to equivalent.</li>
			<li>Variable $shiptotelephone now available for the confirmation template and customer confirmation mail template;</li>
			<li>New variables available to hide or show mandatory fields: $mandatorystate and $mandatorytelephone;</li>
			<li>Telephone number is now included as one of the properties per customer (and can be used in the fe_shipping_info template, use {$telephone_input});</li>
			<li>Store the currency code per order as set up in Options;</li>
			<li>The order detail in the back end shows the email address, the telephone number and the billing address.</li>
		</ul>
	</li>
	<li>Version 0.1.4 - December 10th 2008<br>
		<ul>
			<li>Added link for continue shopping when cart is shown.<br>Use &lt;div ID="continue">{$continueshopping}&lt;/div> somewhere in the fe_showcart template;</li>
			<li>Show SKU and item number in order details in back end;</li>
			<li>Authorized personnel is allowed to delete one order at a time or use mass order deletion. Check permissions on \'Modify Cart Made Simple\'.</li>
		</ul>
	</li>
	<li>Version 0.1.3 - December 4th 2008<br>
		<ul>
			<li>Mail settings added in options tab;</li>
			<li>Mail being send after order confirmation done by customer;</li>
			<li>Additional options to process the mail.</li>
		</ul>
	</li>
	<li>Version 0.1.2 - November 15th 2008<br>
		<ul>
			<li>Maintaining delivery methods is now possible;</li>
			<li>Delivery methods are selectable on the front end;</li>
			<li>If delivery method cost money, this is reflected in the order as such;</li>
			<li>Product number is shown in order detail (back end) for easy reference;</li>
			<li>Extended connection to Payment Made Simple module (for PayPal usage).</li>
		</ul>
	</li>
	<li>Version 0.1.1 - April 28th 2008<br>
		<ul>
			<li>Example stylesheet prepared with all Cart Made Simple tags.</li>
			<li>VAT names and percentages added.</li>
			<li>VAT calculation done. Results are shown on confirmation screen.</li>
			<li>Total nett weight add per order.</li>
			<li>All front end now works with template from database.</li>
		</ul>
	</li>
	<li>Version 0.0.9 - April 18th 2008<br>
		<ul>
			<li>Roll out version prepared.</li>
		</ul>
	</li>
	<li>Version 0.0.4 - April 11th 2008<br>
		<ul>
			<li>New table introduced to hold various \'simple\' deliverymethods. Flat fee on delivery only.</li>
			<li>Preferences extended and are now maintainable in admin part. Formatting amount fields based upon format parameters in admin.</li>;
		</ul>
	</li>
	<li>Version 0.0.3 - April 9th 2008<br>
		<ul>
			<li>Sample templates are added during installation and upgrade.</li>;
		</ul>
	</li>
	<li>Version 0.0.2 - March 24th 2008<br>
		<ul>
			<li>Included tables to store order information.</li>
		</ul>
	</li>
	<li>Version 0.0.1 - March 2nd 2008<br>
		<ul>
			<li>Start date of rebuilding module.</li>
		</ul>
	</li>
</ul>