<html>
<head>
	<title></title>
</head>
<body>
<div style="text-align: center;">
<div></div>

<div style="text-align: left;">
<table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="text-align: center;"><strong style="font-size: 20px; text-align: center;">注文請書</strong></td>
		</tr>
		<tr>
			<td style="text-align: right;"><span style="text-align: right;">$custom-currentdate$&nbsp;</span>$salesorder-salesorder_no$</td>
		</tr>
	</tbody>
</table>
&nbsp;

<table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 350px;">$salesorder-accountid:accountname$ 御中<br />
			<br />
			<span style="font-size:9px;">この度はご用命いただきまして誠にありがとうございます。<br />
			下記の内容につきましてご注文を通り承りました。</span><br />
			<br />
			&nbsp;
			<table border="0" cellpadding="1" cellspacing="1" style="width:250px;">
				<tbody>
					<tr>
						<td><span style="font-size:11px;">御見積番号</span></td>
						<td></td>
						<td style="text-align: right;"><span style="font-size:11px;">$salesorder-quoteid:quote_no$</span></td>
						<td><span style="font-size:11px;">-</span></td>
					</tr>
					<tr>
						<td><span style="font-size:11px;">御見積金額</span></td>
						<td><span style="font-size:11px;">:</span></td>
						<td style="text-align: right;"><span style="font-size:11px;">$salesorder-pre_tax_total$</span></td>
						<td><span style="font-size:11px;">-</span></td>
					</tr>
					<tr>
						<td><span style="font-size:11px;">消費税</span></td>
						<td><span style="font-size:11px;">:</span></td>
						<td style="text-align: right;"><span style="font-size:11px;">$salesorder-tax_totalamount$</span></td>
						<td><span style="font-size:11px;">-</span></td>
					</tr>
					<tr>
						<td><span style="font-size:11px;">合計金額</span></td>
						<td><span style="font-size:11px;">:</span></td>
						<td style="text-align: right;"><span style="font-size:11px;">$salesorder-total$</span></td>
						<td><span style="font-size:11px;">-</span></td>
					</tr>
				</tbody>
			</table>
			</td>
			<td style="width: 150px;"><img alt="" src="test/logo/frevocrm-logo.png" style="text-align: right; width: 200px; height: 40px; float: right;" />$companydetails-organizationname$<br />
			<span style="font-size:10px;">$companydetails-code$<br />
			$companydetails-state$ $companydetails-city$<br />
			$companydetails-address$<br />
			TEL: $companydetails-phone$<br />
			FAX: $companydetails-fax$</span><br />
			&nbsp;</td>
		</tr>
	</tbody>
</table>

<p></p>

<table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
	<tbody>
		<tr>
			<td><span style="font-size: 11px;">$quotes-subject$</span></td>
		</tr>
	</tbody>
</table>
&nbsp;

<table align="left" border="1" cellpadding="1" cellspacing="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="background-color: rgb(238, 238, 238); width: 60%;"><span style="font-size: 10px;">項目</span></td>
			<td style="background-color: rgb(238, 238, 238); width: 10%;"><span style="font-size: 10px;">数量</span></td>
			<td style="background-color: rgb(238, 238, 238); width: 15%;"><span style="font-size: 10px;">単価</span></td>
			<td style="background-color: rgb(238, 238, 238); width: 15%;"><span style="font-size: 10px;">ご提供金額</span></td>
		</tr>
		<tr>
			<td colspan="4"><span style="font-size: 10px;">$loop-products$</span></td>
		</tr>
		<tr>
			<td><span style="font-size:10px;">$salesorder-productid$<br />
			$salesorder-comment$</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-quantity$</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-listprice$</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-producttotal$</span></td>
		</tr>
		<tr>
			<td colspan="4"><span style="font-size: 10px;">$loop-products$</span></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right;"><span style="font-size:10px;">貴社特別値引き</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-discount_amount$</span></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right;"><span style="font-size:10px;">小計</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-subtotal$</span></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: right;"><span style="font-size:10px;">消費税</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-tax_totalamount$</span></td>
		</tr>
		<tr>
			<td colspan="3" rowspan="1" style="text-align: right;"><span style="font-size:10px;">合計</span></td>
			<td style="text-align: right;"><span style="font-size:10px;">$salesorder-total$</span></td>
		</tr>
	</tbody>
</table>

<p></p>

<table border="0" cellpadding="1" cellspacing="1" style="width: 100%;">
	<tbody>
		<tr>
			<td><span style="font-size: 11px;">備考</span></td>
		</tr>
	</tbody>
</table>

<table border="1" cellpadding="1" cellspacing="1" style="width:100%;">
	<tbody>
		<tr>
			<td style="width: 100%;"><span style="font-size:10px;">$salesorder-terms_conditions$</span></td>
		</tr>
	</tbody>
</table>
<br />
&nbsp;</div>
</div>
</body>
</html>
