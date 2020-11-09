
<!--replace  to the correct directory structure -->
<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>The Happy Box::Evoucher</title>
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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0 auto;padding:0;font-family:Calibri;font-size:16px;background:#FFF;">
<div border="0" cellpadding="0" cellspacing="0" style=" max-width:800px;width:100%;margin:0 auto;padding:0;overflow-x:hidden;background:#fff;margin-top:36px;">
	<div style="width:60%;margin:auto;" class="mob_100">
		<table style="width:100%;border:none;" cellspacing="0" cellpadding="0">
			<tr>
				<td style="background:#F4EF14;width:25%;height:5px;"></td>
				<td style="background:#FA683D;width:25%;height:5px;"></td>
				<td style="background:#FF005C;width:25%;height:5px;"></td>
				<td style="background:#10D271;width:25%;height:5px;"></td>
			</tr>
		</table>
		<img src="{{asset('mails/happybox-voucher-cover-img-r@2x.png')}}" alt="" style=" margin:auto;">
	</div>
	<div style="background:#C20A2B;width:100%;height:10px;position:relative;" >
		<img src="{{asset('mails/voucher2_gift.svg')}}" alt="" style=" width:auto;
background:#c20a2b;
padding:10px;
border-radius:50%;
position:absolute;
left:44%;
top:-31px;">
	</div>
	<div style="width:60%;margin:auto;background:#FFFFFF 0% 0% no-repeat padding-box; border:1px solid #C20A2B;border-radius:0px 0px 13px 13px;">
		<p style="text-align:center;font:normal normal bold 20px/19px Calibri;letter-spacing:0px;color:#000000;">
            @foreach( $data as $vc )
            {{$vc['box_voucher']}} {{"  "}}
            @endforeach
        </p>
	</div>
	<div style="width:100%; margin:17px auto 10px;" >
		<img src="{{asset('mails/voucher2_dotted.svg')}}" alt="" style="width:100%;">
	</div>
	<div style="width:60%;margin:auto;" class="mob_100">
		<img src="{{asset('mails/3_steps.svg')}}" alt="" style="margin:auto;width:100%;">
	</div>
	<div style="width:60%;margin:10px auto;">
		<table style="margin-top:20px;margin-bottom:20px;">
			<tbody>
				<tr>
					<td style="width:12%;vertical-align:middle;"><img src="{{asset('mails/step1.svg')}}" alt="" style=""></td>
					<td style="vertical-align:middle;"><p style="padding-left:7px;"> <span style="color:#C20A2B;"> <strong>ACTIVATE</strong></span> your voucher early on <a href="https://happybox.ke/" target="_blank" style="text-decoration:none;font-weight:bold;"><span style="color:#C20A2B;">happybox.ke</span></a> in the<br>
							<a href="{{Config::get('app.client_url')}}/user-dash-activate-voucher.php" style="text-decoration:none;font-weight:bold;" target="_blank"><span style="color:#C20A2B;">register your voucher </span></a> section </p></td>
				</tr>
				<tr>
					<td colspan="2" style="height:20px"></td>
				</tr>
				<tr>
					<td style="width:12%;vertical-align:middle;"><img src="{{asset('mails/step2.svg')}}" alt="" style=""></td>
					<td style="vertical-align:middle;"><p style="padding-left:7px;"> <span style="color:#C20A2B;"> <strong> SELECT</strong></span> your next experience on <a href="https://happybox.ke/" target="_blank" style="text-decoration:none;"><span style="color:#C20A2B;">happybox.ke</span></a> </p></td>
				</tr>
				<tr>
					<td colspan="2" style="height:20px"></td>
				</tr>
				<tr>
					<td style="width:12%;vertical-align:middle;"><img src="{{asset('mails/step3.svg')}}" alt="" style=""></td>
					<td style="vertical-align:middle;"><p style="padding-left:7px;"> <strong><a href="https://happybox.ke/" target="_blank" style="text-decoration:none;"><span style="color:#C20A2B;">BOOK</span></a></strong> your experience with the selected partner and<br>
							share your voucher code, stating you have a HappyBox </p></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="width:60%;margin:10px auto;text-align:center;">
		<p> Your e-voucher is valid for 6 months from the date of purchase </p>
		<p> Early activation of your e-voucher on <strong><a href="https://happybox.ke/" target="_blank" style="text-decoration:none;color:black;">happybox.ke</a> </strong> allows you to benefit from our loss and theft warranty, you will also be able to check the validity status of your e-voucher </p>
		<p> See all the terms and conditions on <strong><a href="https://happybox.ke/" target="_blank" style="text-decoration:none;color:black;">happybox.ke</a> </strong> </p>
	</div>
	<div style="width:60%;margin:auto;padding-top:10px;" class="mob_100">
		<table style="width:100%;border:none;" cellspacing="0" cellpadding="0">
			<tr>
				<td style="background:#F4EF14;width:25%;height:5px;"></td>
				<td style="background:#FA683D;width:25%;height:5px;"></td>
				<td style="background:#FF005C;width:25%;height:5px;"></td>
				<td style="background:#10D271;width:25%;height:5px;"></td>
			</tr>
		</table>
	</div>
</div>
</body>
</html>