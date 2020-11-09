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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0 auto;padding:0;font-family:Calibri;font-size:16px;background:#CCCCCC;">
<div style="width:100%;">
	<div style="width:100%;margin:auto;" class="mob_100">
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
<div border="0" cellpadding="0" cellspacing="0" style=" max-width:800px;
width:100%;
margin:0 auto;
padding:0;
overflow-x:hidden;
background:#fff;
border-top-left-radius:18px;
margin-top:36px;
border-top-right-radius:18px;
border-bottom-right-radius:18px;
border-bottom-left-radius:18px;">
	<div style="width:100%;margin:10px auto;" class="mob_100">
		<img src="{{asset('mails/hb-logo-red_1.svg')}}" alt="" style=" margin:34px auto 20px;">
	</div>
	<div style=" width:93%;margin:10px auto;" class="mob_100">
		<h3 style="text-align:center;font:normal normal bold 20px/24px Calibri;letter-spacing:0px;color:#00ACB3">Dear {{$payload['c_user']}}</h3>
	</div>
	<div style=" width:93%;margin:24px auto -2px;" >
		<img src="{{asset('mails/your_evoucher.svg')}}" alt="" style="width:100%;">
	</div>
	<div style="width:100%;" >
		<img src="{{asset('mails/rect_evoucher.svg')}}" alt="" style="width:100%;">
	</div>
	<div style=" width:93%;margin:0px auto 10px;" >
		<img src="{{asset('mails/this_gift.svg')}}" alt="" style="margin:auto;"> <h2 style="text-align:center;font:normal normal bold 26px/19px Calibri;letter-spacing:0px;color:#000000;margin-top:54px;">{{$payload['c_buyer']}}</h2>
	</div>
	<div style=" width:93%;margin:10px auto;text-align:center;" >
	</div>
	<div style=" width:93%;margin:60px auto 40px;background:#FFFFFF 0% 0% no-repeat padding-box;box-shadow:0px 3px 8px #00000029;" >
		<div style="width:100%;position:relative;">
			<img src="{{asset('mails/post_card.svg')}}" alt="" style="position:absolute;right:0px;top:-57px;">
		</div>
		<div style=" padding:69px 20px 24px;">
			<h3 style="font:normal normal bold 20px/19px Segoe Script;letter-spacing:0px;color:#C20A2B;">Dear Recipient <br></h3>
            <p> Rejoyce! You have been gifted a HappyBox. <br> Attached to this email is your e-voucher, it encloses your voucher code, treat it as you would money. <br> Choose an experience by flipping through the pages of the attached e-booklet, and contact your selected partner to enjoy an unforgettable experience. See you soon on happybox.ke.</p>
		</div>
		<div style="background:#C20A2B 0% 0% no-repeat padding-box;box-shadow:0px 3px 8px #00000029;opacity:1;height:10px;">
		</div>
	</div>
	<div style="width:100%;margin:0px auto 10px;" >
		<img src="{{asset('mails/below_dear.svg')}}" alt="" style="width:100%;">
	</div>
	<div style=" width:93%;margin:0px auto 10px;" >
		<table>
			<tr>
				<td style="width:30%"><img src="{{asset('mails/Box_Mockup_01-200x200@2x.png')}}" alt="" style="width:100%;"></td>
				<td style=" padding-left:14px;"><h3 style="text-align:left;font:normal normal bold 20px/24px Calibri;letter-spacing:0px;color:#C20A2B">{{$payload['box']}}</h3>
                {{$payload['box_description']}}
				</td>
			</tr>
		</table>
	</div>
	<div style=" width:93%;margin:0px auto 10px;" >
	</div>
	<div style="width:93%;margin:auto;border:1px solid #0185B6;">
	</div>
	<div style=" width:80%;padding-top:15px;margin:auto;text-align:center;font:normal normal normal 16px/19px Calibri;letter-spacing:0px;color:#0185B6;padding-bottom:18px;">
		Discover the large range of activities specially selected for you and choose your next<br>
		experience on <a href="https://happybox.ke/" target="_blank" style="font-weight:bold;letter-spacing:0px;color:#0185B6;text-decoration:none !important;">happybox.ke</a>
	</div>
	<div style="width:100%">
		<div style="">
			<img src="{{asset('mails/voucher_bottom.png')}}" style=" margin:0px auto;" alt="">
		</div>
	</div>
	<div style="width:93%;margin:auto;color:#707070;">
		<p style="text-align:center;"> Your attached e-voucher is valid for 6 months after the date of purchase </p>
		<p style="text-align:center;">
		<p style="text-align:center;"> Early activation of your e-voucher on <strong><a href="{{Config::get('app.client_url')}}/user-dash-activate-voucher.php" target="_blank" style="text-decoration:none;"><span style="color:#707070;">happybox.ke</span></a></strong> allows you to benefit from our loss and theft warranty, you will also be able to check the validity status of your e-voucher. </p>
		<p style="text-align:center;">See all the terms and conditions on <a href="https://happybox.ke/" target="_blank" style="text-decoration:none;font-weight:bold;"><span style="color:#707070;">happybox.ke</span></a></p>
		<br>
	</div>
	<div style="width:99%;background:#C20A2B 0% 0% no-repeat padding-box;border-bottom-right-radius:13px;
border-bottom-left-radius:13px; 
padding:9px 6px;
height:39px;
margin:auto;" class="mob_95">
		<div style="width:50%;float:left">
			<img src="{{asset('mails/hb-alt-logo-white.svg')}}" style=" display:inline;" alt=""> <img src="{{asset('mails/Chooseyourgift.svg')}}" style=" display:inline;" alt="">
		</div>
		<div style="width:50%;float:left;text-align:right;">
			<a href="https://www.facebook.com/HappyBoxke-104873668046223" target="_blank"><img src="{{asset('mails/icn-fb-white.svg')}}" style=" display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.instagram.com/happybox.ke/" target="_blank"><img src="{{asset('mails/icn-ig-white.svg')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.linkedin.com/company/happybox-ke/" target="_blank"><img src="{{asset('mails/icn-li-white.svg')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a>
		</div>
	</div>
</div>
<div  style="max-width:800px;
width:100%;
margin:0 auto;
padding:0;
overflow-x:hidden;
background:transparent;
border-top-left-radius:18px;
margin-top:18px;
border-top-right-radius:18px;
margin-bottom:14px;">
	<div style="width:93%;margin:auto;">
		<div style=" text-align:center;margin-bottom:10px;margin-top:1px;">
			<a href="{{Config::get('app.client_url')}}/user-login.php" style="text-align:center;text-decoration:underline;font:normal normal normal 14px/20px Calibri;letter-spacing:0px;color:#999999;" target="_blank">Login to your Account |</a> <a href="{{Config::get('app.client_url')}}/terms.php" style="text-align:center;text-decoration:underline;font:normal normal normal 14px/20px Calibri;letter-spacing:0px;color:#999999;" target="_blank">View our Terms & Conditions |</a> <a href="" style="text-align:center;text-decoration:underline;font:normal normal normal 14px/20px Calibri;letter-spacing:0px;color:#999999;" target="_blank">Unsubscribe</a>
		</div>
	</div>
	<div style=" width:100%;text-align:center;margin-bottom:30px; letter-spacing:0px;color:#999999;float:left;line-height:20px;">
		You are receiving this email because you are a valued customer of HappyBox.<br>
		To unsubscribe from happybox.ke email communications, <a href="#" target="_blank" style="text-decoration:none;color:#999999;"><strong>click here.</strong></a> <br>
		HappyBox | P.O Box 30275| 00100 GPO | Nairobi | Kenya<br>
		Need some help? Have a question? Please send us an email at <a href="mailto:customerservices@happybox.ke"  style="text-decoration:none;color:#999999;font-weight:bold;">customerservices@happybox.ke</a>
	</div>
</div>
</body>
</html>