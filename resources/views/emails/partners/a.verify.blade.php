
<!--replace  to the correct directory structure -->
<html>
<head>
<meta name=viewport content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<title>The Happy Box::Partner Welcome</title>
<style>
/*universal */
img {
	margin: 0;
	padding: 0;
	max-width: 100%;
	display: block;
	border: none;
	outline: none;
}
@media (max-width:767px) {
.mob_100 {
	width: 100% !important;
	padding-left: 10px !important;
	padding-right: 10px !important;
}
.partner_img, .partner_des {
}
.partner_des {
	width: 87%;
	margin-top: 11px;
	padding-left: 9px;
}
.mob_auto_img {
	margin: auto !important;
}
.mob_95 {
	width: 95% !important;
	padding-left: 10px !important;
	padding-right: 10px !important;
}
.mob_hide {
	display: none;
}
.desk_hide {
	display: block;
}
.our_part_img {
	margin-bottom: 8px;
}
}
@media (min-width:768px) {
.mob_hide {
	display: block;
}
.desk_hide {
	display: none;
}
}
/*end universal */
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0 auto;padding:0;font-family:Calibri;font-size:16px;">
<div border="0" cellpadding="0" cellspacing="0" style="max-width:800px;width:100%;margin:0 auto;padding:0;overflow-x:hidden;">
	<div style="width:100%;">
		<a href="https://happybox.ke/" target="_blank"> <img src="{{asset('mails/on_boarding_header.png')}}" alt="" style="padding-bottom:15px;width:100%;"></a>
	</div>
	<div style="width:80%;margin:10px auto;" class="mob_100">
		<h3 style="font:normal normal bold 20px/24px Calibri;letter-spacing:0px;color:#0985B6;opacity:1;text-align:center;">Dear {{ $name }}</h3>
	</div>
	<div style="width:80%; margin:18px auto;" >
		<img src="{{asset('mails/congrats_partnerwelcome.png')}}" alt="" style="width:100%;">
	</div>
	<div style="width:80%;margin:10px auto;text-align:center;" >
		<p> We are happy to welcome you to the HappyBox Partner community; providing you<br>
			with visibility and new customers. </p>
                <p> 
                    To have access to your Partner Portal on <a href="https://happybox.ke/" target="_blank" style="color: black; text-decoration: none;">happybox.ke</a>, please use the link "Activate<br> your account", then input the user name (email address) and the password below.
                
                </p>
                <div style="    width: 61%;margin: 10px auto;text-align: center;border: solid 2px #fa683d;padding: 14px 5px; border-radius: 13px; color: #fa683d;font-weight: bold; line-height: 2;">
                    Username: {{ $email }}<br>Password: {{ $username }}
                </div>
                <p> We strongly recommend changing this password the first time you log in. </p>
                <p> On your Partner Portal you will be able to monitor your HappyBox customers by<br>
			using these following functions: </p>
	</div>
	<div style="width:80%;margin:10px auto;" >
		<table align='center' style="text-align:center;">
			<tr>
				<td style="width:50%;padding-top:30px;vertical-align:top;"><img src="{{asset('mails/icn-functions-03-teal.png')}}" alt="" style="margin:5px auto;">
					<p style="color:#00ACB3;"> Check a voucher code validity, redeem<br>
						a customer voucher code, set a booking date, <br>
						change a booking date,<br>
						cancel a booking. </p></td>
				<td style="padding-top:30px;vertical-align:top;"><img src="{{asset('mails/icn-functions-02-orange.png')}}" alt="" style="margin:5px auto;">
					<p style="color:#FA683D;"> Keep track of the vouchers redeemed <br>
						in your business, and payments made<br>
						to you. </p></td>
			</tr>
			<tr>
				<td style="padding-top:30px;vertical-align:top;"><img src="{{asset('mails/icn-functions-01-blue.png')}}" alt="" style="margin:5px auto;">
					<p style="color:#0185B6;"> Monitor the services you have<br>
						allocated to each box. </p></td>
				<td style="padding-top:30px;vertical-align:top;"><a href="{{ $url }}" target="_blank"> <img src="{{asset('mails/btn-user-activate.png')}}" alt="" style="margin:5px auto;"> </a></td>
			</tr>
		</table>
	</div>
	<div style="width:80%;margin:10px auto;" class="mob_100">
		<div style="">
			<a href="https://happybox.ke/" target="_blank"><img src="{{asset('mails/need_help.png')}}" style=" margin:0px auto;padding-top:30px;" alt=""></a>
		</div>
	</div>
	<div style="width:80%;margin:10px auto;" class="mob_100">
		<p style="color:#0185B6;text-align:center;"> Should you encounter any complications or have any questions, reach out to our<br>
			Partner Care team: </p>
	</div>
	<div style="width:80%;margin:10px auto;" >
		<table align="center"  style="width:100%;">
			<tr>
				<td style="width:50%;padding-top:2px;vertical-align:top;" align="center"><img src="{{asset('mails/icn-mail-blue.png')}}" alt="" style="margin:5px auto;">
					<p > <b>
   <a style="color:#0185B6;text-decoration:none;" href="mailto:director@happybox.ke">director@happybox.ke</a></b> </p></td>
				<td style="padding-top:2px;vertical-align:top;" align="center"><img src="{{asset('mails/icn-phone-blue.png')}}" alt="" style="margin:5px auto;">
					<p > <b>
      <a href="tel:254112454540" target="_blank" style="color:#0185B6;text-decoration:none;">+254 112 454 540
</a></b> </p></td>
			</tr>
		</table>
	</div>
	<div style="width:80%; margin:50px auto 14px;">
		<img src="{{asset('mails/onboarding_your.png')}}" alt="" style="margin:33px auto 4px;">
	</div>
	<div style="width:100%">
		<div style="">
			<a href="https://happybox.ke/" target="_blank"> <img src="{{asset('mails/news_see_you_soon.png')}}" style=" margin:0px auto;padding-top:29px;width:80%;" alt=""></a>
		</div>
	</div>
	<div style="width:100%">
		<div style="">
			<img src="{{asset('mails/news_below_see_you.png')}}" style=" margin:0px auto;width:100%;" alt="">
		</div>
	</div>
	<div style=" width:80%;
background:#C20A2B 0% 0% no-repeat padding-box;
border-bottom-right-radius:13px;
border-bottom-left-radius:13px;
padding:12px 8px 9px;
height:39px;
margin:-1px auto;" class="mob_95">
		<div style="width:50%;float:left">
			<img src="{{asset('mails/hb-alt-logo-white.png')}}" style=" display:inline;" alt=""> <img src="{{asset('mails/Chooseyourgift.png')}}" style=" display:inline;" alt="">
		</div>
		<div style="width:50%;float:left;text-align:right;">
			<a href="https://www.facebook.com/HappyBoxke-104873668046223" target="_blank"><img src="{{asset('mails/icn-fb-white.png')}}" style=" display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.instagram.com/happybox.ke/" target="_blank"><img src="{{asset('mails/icn-ig-white.png')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a> <a href="https://www.linkedin.com/company/happybox-ke/" target="_blank"><img src="{{asset('mails/icn-li-white.png')}}" style="display:inline;height:26px;margin-left:19px;" alt=""></a>
		</div>
	</div>
	
	<div style=" width:100%;text-align:center;margin-top: 30px;margin-bottom:30px; letter-spacing:0px;color:#999999;float:left;line-height:20px;">
		You are receiving this email because you have requested to become a HappyBox partner <br>
		To unsubscribe from happybox.ke partner email communications, <a href="#" target="_blank" style="text-decoration:none;color:#999999;"><strong>click here.</strong></a> <br>
		HappyBox | P.O Box 30275| 00100 GPO | Nairobi | Kenya<br>
		Need some help? Have a question? Please send us an email at <a href="mailto:director@happybox.ke"  style="text-decoration:none;color:#999999;font-weight:bold;">director@happybox.ke</a>
	</div>
</div>
</body>
</html>