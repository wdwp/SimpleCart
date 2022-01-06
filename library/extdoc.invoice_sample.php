<?php
# Module: Cart Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2010 by Duketown
#
# Sample Invoice prepare with FPDF (http://www.fpdf.org/)
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/cartms
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

$gCms = cmsms();
if (!is_object($gCms)) exit;

$db = cmsms()->GetDb();

require(cms_join_path(dirname(__FILE__), '..', 'fpdf184', 'fpdf.php'));

//$uniquedocid = $this->alphaID(9007199254740989);

// Name of the shop is retrieved from Simple Shop preferences for the properties
$organization = '';
$query = 'SELECT * FROM ' . cms_db_prefix() . 'siteprefs WHERE sitepref_name = ?';
$dbresult = $db->Execute($query, array('SimpleShop_mapi_pref_shop_name'));
if ($dbresult && $row = $dbresult->FetchRow()) {
	$organization = $row['sitepref_value'];
}

if (isset($params['organization'])) $organization = $params['organization'];
$author = '';
$query = 'SELECT * FROM ' . cms_db_prefix() . 'siteprefs WHERE sitepref_name = ?';
$dbresult = $db->Execute($query, array('SimpleShop_mapi_pref_admin_name'));
if ($dbresult && $row = $dbresult->FetchRow()) {
	$author = $row['sitepref_value'];
}

$type = 'invoice_prep';
if (isset($params['type'])) $type = $params['type'];
$order_id = 0;
if (isset($params['order_id'])) $order_id = $params['order_id'];
$create_date = 100101;
if (isset($params['create_date'])) $create_date = $params['create_date'];

// Get order information
$orderheader = array();
$orderheader = cartms_utils::GetOrderHeader($order_id);

$shipto = array();
$shipto = $this->orders->GetOrderShipTo($orderheader['customer_id'], false);
$orderlines = array();
$orderlines = $this->orders->GetOrderLines($order_id);

// Translations
$invlabel_invoicefrom = $this->Lang('invoicefrom');
$invlabel_invoice = $this->Lang('inv_invoice');
$invlabel_order = $this->Lang('inv_order');
$invlabel_totalexvat = $this->Lang('title_totalexvat');
$invlabel_totalamount = $this->Lang('label_total_amount', $orderheader['currency'], '');
$invlabel_totalvat = $this->Lang('title_totalvat');
$invlabel_totaldiscount = $this->Lang('discountamount');
$invlabel_adminamount = $this->Lang('adminamount');
$invlabel_shipamount = $this->Lang('title_totalshipping');

$pageformat = 'A4';
$pagewidth = 570; // Points
$lineheight = 15;
// Render the invoice
$x_coord = 60;
$y_coord = 40;
$pdf = new tFPDF('P', 'pt', $pageformat);
$pdf->SetAuthor($author);
$pdf->SetTitle($invlabel_invoicefrom . $organization);
// Prepare the logo to appear
$logo = cms_join_path(dirname(__FILE__), '..', 'images', 'cmsms_newlogo300.jpg');
$pdf->AddPage();
$pdf->Image($logo, $x_coord, $y_coord);
// Add Unicode fonts (.ttf files)
$pdf->AddFont('DejaVu', '', 'DejaVuSans.ttf', true);
$pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
//now use the Unicode font in bold
$pdf->SetFont('DejaVu', 'B', 16);
$pdf->SetXY(430, 60);
$pdf->Cell(120, 40, $invlabel_invoice, 0, 0, 'R');
$pdf->SetFont('DejaVu', '', 10);
$pdf->SetXY(430, 80);
$pdf->Cell(120, 40, $invlabel_invoice . ': ' . $orderheader['invoiceno'], 0, 0, 'R');
$pdf->SetXY(430, 100);
$pdf->Cell(120, 40, $invlabel_order . $order_id, 0, 0, 'R');

// Prepare the address
$x_coord = 20;
$y_coord = 120;
$pdf->SetXY($x_coord, $y_coord);
$tmp = str_ireplace('<br>', '', $shipto['shiptoname']);
if ($tmp != '') {
	$pdf->Cell(180, $lineheight, $tmp);
	$y_coord += $lineheight;
	$pdf->SetXY($x_coord, $y_coord);
}
$tmp = str_ireplace('<br>', '', $shipto['addressstreet']);
if ($tmp != '') {
	$pdf->Cell(180, $lineheight, $tmp);
	$y_coord += $lineheight;
	$pdf->SetXY($x_coord, $y_coord);
}
$tmp = str_ireplace('<br>', '', $shipto['addresszip']);
if ($tmp != '') {
	$pdf->Cell(180, $lineheight, $tmp);
	$y_coord += $lineheight;
	$pdf->SetXY($x_coord, $y_coord);
}
$tmp = str_ireplace('<br>', '', $shipto['addresscity']);
if ($tmp != '') {
	$pdf->Cell(180, $lineheight, $tmp);
	$y_coord += $lineheight;
	$pdf->SetXY($x_coord, $y_coord);
}
$tmp = $shipto['email'];
$pdf->SetX(20);
$pdf->Cell(180, $lineheight, $tmp);
$y_coord += 10 + $lineheight;
$pdf->SetXY($x_coord, $y_coord);

// Draw line with margins of 20
$pdf->SetDrawColor(255, 153, 0);

// Column headings
// Product name
$x_coord = 20;
$pdf->SetXY($x_coord, $y_coord);
$pdf->Cell(280, $lineheight, 'Description', 1);
// Item number
$x_coord += 280;
$pdf->SetX($x_coord);
$pdf->Cell(80, $lineheight, 'Item', 1);
// Quantity
$x_coord += 80;
$pdf->SetX($x_coord);
$pdf->Cell(30, $lineheight, 'Qty', 1, 0, 'R');
// Unit Price
$x_coord += 30;
$pdf->SetX($x_coord);
$pdf->Cell(50, $lineheight, 'Price', 1, 0, 'R');
// Line amount
$x_coord += 50;
$pdf->SetX($x_coord);
$pdf->Cell(100, $lineheight, 'Amount', 1, 0, 'R');
$y_coord += $lineheight;
$pdf->Line(20, $y_coord, $pagewidth - 20, $y_coord);
//$y_coord += 20;
$pdf->SetY($y_coord);
foreach ($orderlines as $orderline) {
	// Product name
	$x_coord = 20;
	$pdf->SetX($x_coord);
	$pdf->Cell(280, $lineheight, $orderline->categoryname);
	// Item number
	$x_coord += 280;
	$pdf->SetX($x_coord);
	$pdf->Cell(80, $lineheight, $orderline->itemnumber);
	// Quantity
	$x_coord += 80;
	$pdf->SetX($x_coord);
	$pdf->Cell(30, $lineheight, $orderline->qty, 0, 0, 'R');
	// Unit Price
	$x_coord += 30;
	$pdf->SetX($x_coord);
	$pdf->Cell(50, 15, $orderline->price, 0, 0, 'R');
	// Line amount
	$x_coord += 50;
	$pdf->SetX($x_coord);
	$pdf->Cell(100, 15, $orderline->lineamount, 0, 0, 'R');
	$x_coord = 20;
	$y_coord += $lineheight;
	$pdf->SetXY($x_coord, $y_coord);
	if ($orderline->productname != '') {
		$pdf->Cell(280, $lineheight, $orderline->productname);
		$x_coord = 20;
		$y_coord += $lineheight;
		$pdf->SetXY($x_coord, $y_coord);
	}
	if ($orderline->attributename != '') {
		$pdf->Cell(280, $lineheight, $orderline->attributename);
		$x_coord = 20;
		$y_coord += $lineheight;
		$pdf->SetXY($x_coord, $y_coord);
	}
	$y_coord += 5;
	$pdf->SetXY($x_coord, $y_coord);
}

// Prepare the footer
$x_coord = 360;
$totals_x_coord = $x_coord;
$y_coord = 650;

if ($orderheader['totaldiscount'] != 0) {
	// Now the total VAT amount
	$pdf->SetXY($x_coord, $y_coord);
	$pdf->Cell(100, $lineheight, $invlabel_totaldiscount, 1, 0, 'R');
	$x_coord += 100;
	$pdf->SetXY($x_coord, $y_coord);
	// Make sure discount is shown negative
	$totaldiscount = 0 - $orderheader['totaldiscount'];
	$formattedamount = $this->orders->FormatAmount($totaldiscount);
	$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
	$x_coord = $totals_x_coord;
	$y_coord += $lineheight;
}

// If tax calculated, show the tax/VAT total amount
$totalvatamount = $orderheader['totalvat0amount'] + $orderheader['totalvat1amount']
	+ $orderheader['totalvat2amount'] + $orderheader['totalvat3amount']
	+ $orderheader['totalvat4amount'];
if ($totalvatamount != 0) {
	// Print of the total amount excl. VAT
	$pdf->SetXY($x_coord, $y_coord);
	$pdf->Cell(100, $lineheight, $invlabel_totalexvat, 1, 0, 'R');
	$x_coord += 100;
	$pdf->SetXY($x_coord, $y_coord);
	$formattedamount = $this->orders->FormatAmount($orderheader['totalproduct']);
	$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
	$x_coord = $totals_x_coord;
	$y_coord += $lineheight;
	// Now the total VAT amount
	$pdf->SetXY($x_coord, $y_coord);
	$pdf->Cell(100, $lineheight, $invlabel_totalvat, 1, 0, 'R');
	$x_coord += 100;
	$pdf->SetXY($x_coord, $y_coord);
	$formattedamount = $this->orders->FormatAmount($totalvatamount);
	$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
	$x_coord = $totals_x_coord;
	$y_coord += $lineheight;
}
// If any administration costs, show them
if ($orderheader['totaladmincost'] != 0) {
	$pdf->SetXY($x_coord, $y_coord);
	$pdf->Cell(100, $lineheight, $invlabel_adminamount, 1, 0, 'R');
	$x_coord += 100;
	$pdf->SetXY($x_coord, $y_coord);
	$formattedamount = $this->orders->FormatAmount($orderheader['totaladmincost']);
	$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
	$x_coord = $totals_x_coord;
	$y_coord += $lineheight;
}
// If any shipping costs, show them
if ($orderheader['totalshipping'] != 0) {
	$pdf->SetXY($x_coord, $y_coord);
	$pdf->Cell(100, $lineheight, $invlabel_shipamount, 1, 0, 'R');
	$x_coord += 100;
	$pdf->SetXY($x_coord, $y_coord);
	$formattedamount = $this->orders->FormatAmount($orderheader['totalshipping']);
	$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
	$x_coord = $totals_x_coord;
	$y_coord += $lineheight;
}
// Now that all additional amount have been printed, print the total amount
$pdf->SetXY($x_coord, $y_coord);
$pdf->Cell(100, $lineheight, $invlabel_totalamount, 1, 0, 'R');
$x_coord += 100;
$pdf->SetFont('DejaVu', 'B');
$pdf->SetXY($x_coord, $y_coord);
$formattedamount = $this->orders->FormatAmount($orderheader['totalproduct']
	- $orderheader['totaldiscount']
	+ $orderheader['totalshipping'] + $orderheader['totaladmincost']
	+ $totalvatamount);
$pdf->Cell(100, $lineheight, $formattedamount, 1, 0, 'R');
$pdf->SetFont('DejaVu');

$invoicepath = cms_join_path($this->config['uploads_path'], $this->getName(), $orderheader['invoiceno'] . '.pdf');

// Prepare the generated pdf to the correct output
switch ($type) {
	case 'invoice_prep':
		$pdf->Output($invoicepath, 'F');
		$params = array(
			'active_tab' => 'order', 'orderstatus' => 'INV',
			'tab_message' => $this->Lang('extdocprep_invoice')
		);
		// Redirect the user to the default admin screen
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
		break;
	case 'invoice_fe':
		// Invoice has been prepared for front end so redirecting is done somewere else
		$pdf->Output($invoicepath, 'F');
		return;
		break;
}
