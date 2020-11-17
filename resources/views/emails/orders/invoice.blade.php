
<!--replaceto the correct directory structure -->
<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>The Happy Box::Invoice</title>
<style>
/*universal */
img {
	margin:0;
	padding:0;
	max-width:100%;
	display:block;
	border:none;
	outline:none;
}
 @media (max-width:767px) {
.mob_100 {
	width:100% !important;
	padding-left:10px !important;
	padding-right:10px !important;
}
.partner_img, .partner_des {
}
.partner_des {
	width:87%;
	margin-top:11px;
	padding-left:9px;
}
.mob_auto_img {
	margin:auto !important;
}
.mob_95 {
	width:95% !important;
	padding-left:10px !important;
	padding-right:10px !important;
}
.mob_hide {
	display:none;
}
.desk_hide {
	display:block;
}
.our_part_img {
	margin-bottom:8px;
}
}
 @media (min-width:768px) {
.mob_hide {
	display:block;
}
.desk_hide {
	display:none;
}
}
/*end universal */
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0 auto;padding:0;font-family:Calibri;font-size:16px;background:#E6E6E6;">
<div border="0" cellpadding="0" cellspacing="0" style=" max-width:800px;width:100%;margin:0 auto;padding:0;overflow-x:hidden;background:#fff;margin-top:36px;">
	<div style="width:90%;margin:auto;padding-top:12px;padding-bottom:12px;" class="mob_100">
		<table style="width:100%;border:none;" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:50%;vertical-align:middle;"><span style="color:#c20a2b;font-size:49px;font-weight:bold;">INVOICE</span></td>
				<td style="vertical-align:middle;" alig="right"><img src="{{public_path('mails/happy_logo.png')}}" alt="" style=" width:auto;float:right;height:70px;"/></td>
			</tr>
		</table>
	</div>
	<div style="width:90%;margin:auto;padding-top:12px;padding-bottom:12px;" class="mob_100">
		<table style="width:100%;border:none;margin-bottom:50px;" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:35%;vertical-align:top;"><span style="margin-bottom:8px;display:block;background:#00acb3;color:white;font-weight:bold;padding:7px 30px;border-radius:7px;">BILL FROM </span> <span style="font-size:20px;font-weight:bold;">HAPPYBOX</span><br>
					<span>P.O. BOX 30275 – Nairobi 00100</span><br>
					<span><strong>PIN No.</strong> P051767160R</span></td>
				<td style="width:30%;"></td>
				<td style="width:35%;vertical-align:top;" alig="right"><span style="margin-bottom:8px;display:block;background:#00acb3;color:white;font-weight:bold;padding:7px 30px;border-radius:7px;">BILL TO </span> 
                @php( $umeta = \App\Userinfo::where('internal_id', $data->customer_buyer)->first() )
                @php( $shipmeta = \App\Shipping::where('customer_id', $data->customer_buyer)->first() )
                @if(!is_null($umeta))
                    <span style="font-size:20px;font-weight:bold;">{{ $umeta->fname }} {{ $umeta->sname }}</span>
                @else
                    <span style="font-size:20px;font-weight:bold;">Unknown Name</span>
                @endif
                <br>
                @if(!is_null($shipmeta))
					<span>{{ $shipmeta->address }}</span>
                @else
                    <span>No shipping info defined</span>
                @endif
                </td>
			</tr>
		</table>
		<div style="width:100%;">
            Order Number: <b>{{ $data->order_id }}</b><br>
            Order Amount: <b>KES {{ number_format(floor($data->order_totals), 2) }}</b><br>
            Paid Amount: <b>KES {{ number_format(floor($data->paid_amount), 2) }}</b><br>
            Payment Method: <b>{{ $data->payment_method }}</b><br>
		</div>
		<table style="width:100%;border:none;margin-top:50px;" cellspacing="0" cellpadding="0">
			<tr>
				<td style="" align="right"><span style=" text-align:left;font:normal normal bold 20px/45px Segoe Script;letter-spacing:0px;color:#FFFFFF;text-shadow:0px 3px 6px #00000029;
 background:#00acb3;
 border-radius:6px;
 padding:2px 8px;">Thank you for your business! </span></td>
			</tr>
		</table>
		<div style="width:100%;margin:auto;color:#999999;padding-top:98px;text-align:center;" class="mob_100">
                    <p > If you have any questions about this invoice, please contact us <br>
			by email <a href="mailto:customerservices@happybox.ke" style="color:#999999;">customerservices@happybox.ke</a> or by phone <a style="color:#999999;" href="tel:254112454540">+254 112 454 540 </a> </p>
		</div>
	</div>
</div>
</body>
</html>