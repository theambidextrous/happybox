<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>The Happy Box::Forgot Password</title>
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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0 auto;padding:0;font-family:Calibri;font-size:16px;">
<div border="0" cellpadding="0" cellspacing="0" style="max-width:800px;width:100%;margin:0 auto;padding:0;overflow-x:hidden;">
   <!-- begin new header-->
	<div style="width:100%; text-align: center;">
		<a href="https://happybox.ke/" target="_blank"> <img src="{{asset('mails/customer_head.png')}}" alt="" style="margin: auto;"></a>
	</div>
   
    <div style="width:80%;margin:10px auto;" class="mob_100">
		<h3 style="font: normal normal bold 20px/24px Calibri; letter-spacing: 0px;color: #189ed0;opacity: 1; text-align: center;">Dear {{ $name }}</h3>
	</div>
    <!-- end new header-->
	<div style="width:80%;margin:18px auto; text-align: center;" >
		<img src="{{asset('mails/customer_welcome.png')}}" alt="" style="margin:0px auto;">
	</div>
	<div style="width:80%;margin:10px auto;text-align:center;" >
		<p> A password reset was requested for the HappyBox customer account associated <br>with this email address.</p>
		<p> If you did not request this then please ignore this email otherwise click below to set<br>
			a new password. </p>
	</div>
	<div style="width:80%;margin:18px auto;text-align:center;" >
		<a href="{{ $url }}" target="_blank"> <img src="{{asset('mails/btn-password-reset.png')}}" alt="" style=" margin:auto;"> </a>
	</div>
    
    <!--new footer-->
	<div style="width:80%;margin:20px auto; text-align: center;">
		<div style="">
			<img src="{{asset('mails/welcome_happy.png')}}" style="margin:17px auto;" alt="">
		</div>
	</div>
	
	<div style="width:100%;text-align: center;">
		
			<img src="{{asset('mails/Group5286@2x.png')}}" style=" margin:0px auto;" alt="">
		
	</div>
    
	<div style=" width:98%;
background:#C20A2B 0% 0% no-repeat padding-box;
border-bottom-right-radius:13px;
border-bottom-left-radius:13px;
padding:12px 8px 9px;
height:39px;
margin:-2px auto;" class="mob_95">
		<div style="width:50%;float:left">
			<img src="{{asset('mails/hb-alt-logo-white.png')}}" style=" display:inline;" alt=""> <img src="{{asset('mails/Chooseyourgift.png')}}" style=" display:inline;" alt="">
		</div>
		<div style="width:50%;float:left;text-align:right;">
			<a href="https://www.facebook.com/HappyBoxke-104873668046223" target="_blank"><img src="{{asset('mails/icn-fb-white.png')}}" style=" display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.instagram.com/happybox.ke/" target="_blank"><img src="{{asset('mails/icn-ig-white.png')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.linkedin.com/company/happybox-ke/" target="_blank"><img src="{{asset('mails/icn-li-white.png')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a>
		</div>
	</div>
	<div style="width:100%;float:left;">
		<div style="text-align:center;margin-bottom:10px;margin-top:10px;">
			<a href="{{Config::get('app.client_url')}}/user-login.php" style="text-align:center;text-decoration:underline;font:normal normal normal 14px/20px Calibri;letter-spacing:0px;color:#999999;" target="_blank">Login to your Account |</a> <a href="{{Config::get('app.client_url')}}/terms.php" style="text-align:center;text-decoration:underline;font:normal normal normal 14px/20px Calibri;letter-spacing:0px;color:#999999;" target="_blank">View our Terms & Conditions </a> 
		</div>
	</div>
	<div style="width:100%;text-align:center;margin-bottom:30px;letter-spacing:0px;color:#999999;float:left;line-height:20px;">
		You are receiving this email because you are a valued customer of HappyBox.<br>
		
		HappyBox | P.O Box 30275| 00100 GPO | Nairobi | Kenya<br>
		Need some help? Have a question? Please send us an email at <a href="mailto:customerservices@happybox.ke" style="text-decoration:none;color:#999999;font-weight:bold;">customerservices@happybox.ke
</a>
	</div>
    <!--end new footer-->
</div>
</body>
</html>
    