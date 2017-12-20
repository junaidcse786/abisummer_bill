<?php 
	
	$script="";

	require_once('../config/dbconnect.php');	
	
	require_once('function.php');

	if(isset($_SESSION["admin_panel"])){

		header('Location: '.SITE_URL_ADMIN);

	}

	$alert_message=""; $alert_box_show="hide"; $alert_type="success";
	
	$err_easy="has-error";
	
	$user_email="";
	$user_org_name="";
	$user_start_date="";
	$user_end_date="";
	$code="";
	$user_name="";
	$user_first_name="";
	$user_last_name="";
	$user_password="";
	$cpassword="";
	
	$messages = array(
				  'user_email' => array('status' => '', 'msg' => ''),
				  'code' => array('status' => '', 'msg' => ''),
				  'user_password' => array('status' => '', 'msg' => ''),
				  'cpassword' => array('status' => '', 'msg' => ''),
				  'user_name' => array('status' => '', 'msg' => ''),
				  'user_first_name' => array('status' => '', 'msg' => ''),
				  'user_last_name' => array('status' => '', 'msg' => '')				  
				);
						

	if(isset($_POST["login"])){

			extract($_POST);			

			$dd = mysqli_query($db, "select * from ".$db_suffix."user where (user_email='$user_email' OR user_name='$user_email') AND user_password='".md5($user_password)."'");

				if(mysqli_num_rows($dd)>0){					

					$result1=mysqli_fetch_array($dd); 
						
					$today=strtotime(date("Y-m-d"));
					$start_date=strtotime($result1["user_validity_start"]);
					$end_date=strtotime($result1["user_validity_end"]);
					
					
					$query_status_chk = mysqli_query($db, "select role_id from ".$db_suffix."role where role_id='".$result1["role_id"]."' AND role_status='0'");

					if(mysqli_num_rows($query_status_chk)>0){
					
						$alert_box_show="show";
						$alert_type="danger";
						$alert_message="Ihr Konto ist inaktiv. Bitte wenden Sie sich an die Administratoren.";				
					}
					
					
					if($result1["user_status"]==0)

						{
							$alert_box_show="show";
							$alert_type="danger";
							$alert_message="Ihr Konto ist inaktiv. Bitte wenden Sie sich an die Administratoren.";							
						}	

					if($result1["user_status"]==2)

						{
							$alert_box_show="show";
							$alert_type="danger";
							$alert_message="Your account has been banned. Please contact the Admin";							
						}
					
					if($result1["role_id"]!=8 && $result1["role_id"]!=15)

						{
							$alert_box_show="show";
							$alert_type="danger";
							$alert_message="Zu diesem Bereich haben Sie keinen Zutritt.";							
						}
						
					if(!($today>=$start_date && $today<=$end_date) && $result1["role_id"]!=8)
					
					{
						$alert_box_show="show";
						$alert_type="danger";
						$alert_message="Ihr Konto ist noch nicht bzw. nicht mehr freigeschaltet oder bereits abgelaufen.";			
					}
						
					if($alert_message=='')	

					{

						$_SESSION["user_email"] = $result1["user_email"];
	
						$_SESSION["site_name"] = SITE_NAME;
						
						$_SESSION["admin_panel"] = 1;
	
						$_SESSION["user_id"] = $result1["user_id"];
	
						$_SESSION["user_name"] = $result1["user_name"];
						
						$_SESSION["user_org_name"] = $result1["user_org_name"];
						
						$_SESSION["user_level"] = $result1["user_level"];
						
						$_SESSION["role_id"] = $result1["role_id"];
						
						header('Location: '.SITE_URL_ADMIN);
	
						/* if(isset($_SESSION["accessing_url"])){
						
							$accessing_URI=$_SESSION["accessing_url"];
							$_SESSION["accessing_url"]='';
							unset($_SESSION["accessing_url"]);
							header('Location: '.$accessing_URI);
						}	
						else
						
							header('Location: '.SITE_URL_ADMIN); */	

					}
			}
				else {				
					$alert_box_show="show";
					$alert_type="danger";
					$alert_message="Benutzername und Passwort stimmen nicht überein.";					
				}
			}	

				
if(isset($_POST["forget_password"])){
				
	extract($_POST);
	
	$script="<script>jQuery('.login-form').hide(); jQuery('.forget-form').show();</script>";
	
	$dd = mysqli_query($db, "select * from ".$db_suffix."user where user_email='$user_email' OR user_name='$user_email'");

	if(mysqli_num_rows($dd)>0){	
	
		$result1=mysqli_fetch_array($dd); 
	
		/*require_once('phpmailer/class.phpmailer.php');*/
		
		$forget_pass_name=$result1["user_first_name"]." ".$result1["user_last_name"];
		
		//<br />Noch einen schönen Tag !
			
		$subject="Ihr Passwort auf ".SITE_NAME;
		$to=$result1["user_email"];
		
		$message="<p>Sehr geehrte ".$forget_pass_name."<br /><br />
		
		Ihr Passwort ist: ".$result1["user_password"]."<br /><br />
		
		Viel Spaß!<br /><br /><br />
		
		Viele Grüße<br />
		
		".SITE_NAME." TEAM
		
		</p>";
					
		 $header = "From: ".SITE_NAME." <".SITE_EMAIL."> \r\n";
		 $header .= "MIME-Version: 1.0\r\n";
		 $header .= "Content-type: text/html; charset=UTF-8\r\n";
		 
		 $retval = mail ($to,$subject,$message,$header);
         
         if( $retval == true ) {
          
		    $alert_box_show="show";
			$alert_type="success";
			$alert_message="Bitte überprüfen Sie Ihre E-Mail, um Ihr Passwort zu erhalten.";
         }
		
		
		/*$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->SMTPSecure = "ssl";  
		$mail->Host       = "smtp.gmail.com"; 
		$mail->Port       = 465;

		$mail->Username   = "sahid.info@gmail.com" ;     // SMTP server username
		$mail->Password   = "" ;            // SMTP server password

		try 

		{
			$mail->AddReplyTo(SITE_EMAIL, $subject);
			$mail->AddAddress($user_email, $subject);
			$mail->SetFrom(SITE_EMAIL,SITE_NAME);
			$mail->Subject = $subject;
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			$mail->MsgHTML($message);
			$mail->Send();
		} 
		catch (phpmailerException $e) 
		{
			//echo $msg = $e->errorMessage(); //Pretty error messages from PHPMailer
		} 
		catch (Exception $e) 
		{
			//echo  $msg = $e->getMessage(); //Boring error messages from anything else!
		}*/
	}
	else{
	
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Benutzername oder E-Mail-Adresse unbekannt.";
	}
}	


if(isset($_POST["sign_up"])){

	$err=0;
				
	extract($_POST);
	
	$script="<script>jQuery('.login-form').hide(); jQuery('.register-form').show();</script>";
	
	if(empty($user_email))
	{
		$messages["user_email"]["status"]=$err_easy;
		$messages["user_email"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	else if(!isEmail($user_email)){
		$messages["user_email"]["status"]=$err_easy;
		$messages["user_email"]["msg"]="Ungültige E-Mail-Adresse.";;
		$err++;
	
	}
	else
	{
		$dd = mysqli_query($db, "select user_id from ".$db_suffix."user where user_email='$user_email'");

		if(mysqli_num_rows($dd)>0){
		
			$messages["user_email"]["status"]=$err_easy;
			$messages["user_email"]["msg"]="Diese E-Mail-adresse wird schon verwendet.";
			$err++;		
		}
	}
	if(empty($code))
	{
		$messages["code"]["status"]=$err_easy;
		$messages["code"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	else
	{
		$dd = mysqli_query($db, "select c.codes_id from ".$db_suffix."codes c
		Left Join ".$db_suffix."indiv_codes ic on ic.codes_id=c.codes_id where ic.codes_value='$code' AND c.codes_status='1' AND CURDATE()>=c.codes_start_date AND CURDATE()<=c.codes_end_date AND c.codes_quantity!='0' AND c.codes_stud='0'");

		if(mysqli_num_rows($dd)<=0){
		
			$messages["code"]["status"]=$err_easy;
			$messages["code"]["msg"]="Ungültiger Code.";;
			$err++;		
		}
		else
		{
			$sql = "select * from ".$db_suffix."codes c
		Left Join ".$db_suffix."indiv_codes ic on ic.codes_id=c.codes_id where ic.codes_value='$code' AND c.codes_status='1' AND CURDATE()>=c.codes_start_date AND CURDATE()<=c.codes_end_date AND c.codes_quantity!='0' AND c.codes_stud='0'";				
			$query = mysqli_query($db, $sql);
			if(mysqli_num_rows($query) > 0)
			{
				$content     = mysqli_fetch_object($query);
				$codes_id= $content->codes_id;
				$user_org_name       = $content->codes_org_name;
				$user_level       = $content->codes_level;
				$user_start_date   = $content->codes_start_date;	
				$user_end_date = $content->codes_end_date;
			}		
		}
	}
	
	
	if(empty($user_name))
	{
		$messages["user_name"]["status"]=$err_easy;
		$messages["user_name"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	else
	{
		$dd = mysqli_query($db, "select user_id from ".$db_suffix."user where user_name='$user_name'");

		if(mysqli_num_rows($dd)>0){
		
			$messages["user_name"]["status"]=$err_easy;
			$messages["user_name"]["msg"]="Dieser Benutzername  wird schon verwendet.";;
			$err++;		
		}
	}
	
	if(empty($user_first_name))
	{
		$messages["user_first_name"]["status"]=$err_easy;
		$messages["user_first_name"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	
	if(empty($user_last_name)){
		$messages["user_last_name"]["status"]=$err_easy;
		$messages["user_last_name"]["msg"]="Dieses Feld muss ausgefüllt werden.";;
		$err++;		
	}
	if($user_password!=$cpassword || empty($user_password)){
		$messages["user_password"]["status"]=$err_easy;
		$messages["cpassword"]["status"]=$err_easy;
		$messages["cpassword"]["msg"]="Passwörter stimmen nicht überein.";;
		$err++;		
	}
	
	if($err==0){	
		$sql_user="INSERT INTO ".$db_suffix."user (role_id, user_email, user_first_name, user_last_name, user_name, user_password, user_status, user_creation_date, user_org_name, user_validity_start, user_validity_end, user_trackability, user_level) values ('15','$user_email', '$user_first_name', '$user_last_name', '$user_name', '$user_password', '1','".date('Y-m-d H:i:s')."', '$user_org_name', '$user_start_date', '$user_end_date','1', '$user_level')" ;
		mysqli_query($db,$sql_user);
		
		$dd = mysqli_query($db, "UPDATE ".$db_suffix."codes SET codes_quantity=codes_quantity-1 where codes_id='$codes_id'");
		
		$dd = mysqli_query($db, "DELETE FROM ".$db_suffix."indiv_codes where codes_value='$code'");
		
		$user_email="";
		$user_name="";
		$user_first_name="";
		$user_last_name="";
		$user_password="";
		$cpassword="";
		$code="";
		$user_org_name="";
		$user_start_date="";
		$user_end_date="";
	
		$alert_box_show="show";
		$alert_type="success";
		$alert_message="Bitte jetzt anmelden.";	
	}
	else{
		$alert_box_show="show";
		$alert_type="danger";
		$alert_message="Bitte korrigieren Sie folgenden Fehler.";	
	}
	
}		
	
					
			
			
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
	<meta charset="utf-8" />
	<title>LOGIN | <?php echo SITE_NAME; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	<meta name="MobileOptimized" content="320">
	<!-- BEGIN GLOBAL MANDATORY STYLES -->
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
	<!-- END PAGE LEVEL SCRIPTS -->
	<!-- BEGIN THEME STYLES -->
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
	<link href="<?php echo SITE_URL_ADMIN; ?>/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>damn.ico"/>

</head>
<!-- BEGIN BODY -->
<body class="login">

<!-- BEGIN LOGO -->

<?php 
		
	$cat_sql = "SELECT * FROM ".$db_suffix."logo";
	$cat_query = mysqli_query($db,$cat_sql);
	while($row = mysqli_fetch_object($cat_query))
   {
	$logo=$row->banner_image;
   }
	
?>


	<div class="logo">
		<a href="<?php echo SITE_URL_ADMIN; ?>login.php"><img src="<?php echo SITE_URL; ?>images/<?php echo $logo; ?>" alt="Admin" /></a>
	</div>
	<!-- END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="content">
		<!-- BEGIN LOGIN FORM -->
		
		<?php if(!isset($_POST["forget_password"]) && !isset($_POST["sign_up"])) { ?>
		
		<form class="login-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
			<h3 class="form-title">Melden Sie sich bei Ihrem Konto an</h3>
			
			<div class="alert alert-<?php echo $alert_type; ?> <?php echo $alert_box_show; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <?php echo $alert_message; ?>
            </div>
			
			<div class="form-group <?php echo $messages["user_email"]["status"] ?>">
				<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
				<label class="control-label visible-ie8 visible-ie9">Benutzername oder E-Mail-Adresse</label>
				<div class="input-icon">
					<i class="fa fa-user"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Benutzername oder E-Mail-Adresse" name="user_email"/>
				</div>
				<span for="user_email" class="help-block"><?php echo $messages["user_email"]["msg"] ?></span>
			</div>
			
			<div class="form-group <?php echo $messages["user_password"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Passwort</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Passwort" name="user_password"/>
				</div>
				<span for="user_password" class="help-block"><?php echo $messages["user_password"]["msg"] ;?></span>
			</div>
			
			<div class="form-actions">
				<!--<label class="checkbox">
				<input type="checkbox" name="remember" value="1"/> Remember me
				</label>-->
				<button type="submit" class="btn green pull-right" name="login">
				Anmelden <i class="fa fa-sign-in"></i>
				</button>            
			</div>
            <div class="forget-password">
				
					<h4>Sie haben noch kein Konto?</h4>
					<p>Klicken Sie <a href="javascript:;" id="register-btn" ><strong>hier, um ein Konto</strong></a> zu erstellen.
				</p>
			</div>
			<div class="forget-password">
				<h4>Passwort vergessen ?</h4>
				<p>
					Klicken Sie <a href="javascript:;"  id="forget-password"><strong>hier</strong></a>, um Ihr Passwort abzurufen.
				</p>
			</div>
			
		</form>
		
		<?php } ?>
		
		<!-- END LOGIN FORM -->        
		<!-- BEGIN FORGOT PASSWORD FORM -->
		
		<?php if(!isset($_POST["sign_up"])) { ?>
		
		<form class="forget-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
			<h3 >Passwort vergessen ?</h3>
			<p>Geben Sie Ihre E-Mail-Adresse ein, um Ihr Kennwort abzurufen.</p>
			
			<div class="alert alert-<?php echo $alert_type; ?> alert-dismissable <?php echo $alert_box_show; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <?php echo $alert_message; ?>
            </div>
			
			<div class="form-group <?php echo $messages["user_email"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Benutzername oder E-Mail-Adresse</label>
				<div class="input-icon">
					<i class="fa fa-user"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Benutzername oder E-Mail-Adresse" name="user_email"/>
				</div>
				<span for="user_email" class="help-block"><?php echo $messages["user_email"]["msg"] ?></span>
			</div>
			
			<div class="form-actions">
				<a href="<?php echo SITE_URL_ADMIN.'login.php'; ?>"><button type="button" id="back-btn" class="btn">
				<i class="m-fa fa-swapleft"></i> Zurück
				</button></a>
				<button type="submit" class="btn green pull-right" name="forget_password">
				Weiter <i class="m-fa fa-swapright m-fa fa-white"></i>
				</button>            
			</div>
		</form>
		
		<?php } ?>
		<!-- END FORGOT PASSWORD FORM -->
		<!-- BEGIN REGISTRATION FORM -->
		
		<form class="register-form" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
			<h3 >Registrieren</h3>
			<p>Geben Sie Ihre persönlichen Daten ein:</p>
			
			<div class="alert alert-<?php echo $alert_type; ?> alert-dismissable <?php echo $alert_box_show; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                        <?php echo $alert_message; ?>
            </div>
			
			<div class="form-group <?php echo $messages["user_first_name"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Vorname</label>
				<div class="input-icon">
					<i class="fa fa-font"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Vorname" value="<?php echo $user_first_name ?>" name="user_first_name"/>
				</div>
				<span for="user_first_name" class="help-block"><?php echo $messages["user_first_name"]["msg"] ?></span>
			</div>
			
			<div class="form-group <?php echo $messages["user_last_name"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Nachname</label>
				<div class="input-icon">
					<i class="fa fa-font"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Nachname" value="<?php echo $user_last_name ?>" name="user_last_name"/>
				</div>
				<span for="user_last_name" class="help-block"><?php echo $messages["user_last_name"]["msg"] ?></span>
			</div>
			
			<div class="form-group <?php echo $messages["user_email"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">E-Mail-Adresse</label>
				<div class="input-icon">
					<i class="fa fa-envelope"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="E-Mail-Adresse" value="<?php echo $user_email ?>" name="user_email"/>
				</div>
				<span for="user_email" class="help-block"><?php echo $messages["user_email"]["msg"] ?></span>
			</div>
			
			<div class="form-group <?php echo $messages["user_name"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Benutzername</label>
				<div class="input-icon">
					<i class="fa fa-user"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Benutzername" value="<?php echo $user_name ?>" name="user_name"/>
				</div>
				<span for="user_name" class="help-block"><?php echo $messages["user_name"]["msg"] ?></span>
			</div>
			
			<div class="form-group <?php echo $messages["user_password"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Passwort</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Passwort" value="<?php echo $user_password ?>" name="user_password"/>
				</div>
				<span for="user_password" class="help-block"><?php echo $messages["user_password"]["msg"] ;?></span>
			</div>
			
			<div class="form-group <?php echo $messages["cpassword"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Passwort bestätigen</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Passwort bestätigen" value="<?php echo $cpassword ?>" name="cpassword"/>
				</div>
				<span for="cpassword" class="help-block"><?php echo $messages["cpassword"]["msg"] ;?></span>
			</div>
			
			<div class="form-group <?php echo $messages["code"]["status"] ?>">
				<label class="control-label visible-ie8 visible-ie9">Code</label>
				<div class="input-icon">
					<i class="fa fa-lock"></i>
					<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Code" value="<?php echo $code ?>" name="code"/>
				</div>
				<span for="code" class="help-block"><?php echo $messages["code"]["msg"] ;?></span>
			</div>
			
			<div class="form-actions">			
				<a href="<?php echo SITE_URL_ADMIN.'login.php'; ?>"><button id="register-back-btn" type="button" class="btn">
				<i class="m-fa fa-swapleft"></i>  Zurück
				</button></a>				
				<button type="submit" id="register-submit-btn" class="btn green pull-right" name="sign_up">
				Weiter <i class="m-fa fa-swapright m-fa fa-white"></i>
				</button>            
			</div>
		</form>
		<!-- END REGISTRATION FORM -->
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		<?php echo FOOTER_TEXT; ?>
	</div>
	<!-- END COPYRIGHT -->
	<!--[if lt IE 9]>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/respond.min.js"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/excanvas.min.js"></script> 
	<![endif]-->
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
	<!-- END CORE PLUGINS -->
	<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo SITE_URL_ADMIN; ?>assets/global/plugins/select2/select2.min.js"></script>
	<!-- END PAGE LEVEL PLUGINS -->
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/global/scripts/metronic.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
	<script src="<?php echo SITE_URL_ADMIN; ?>assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
	<!-- END PAGE LEVEL SCRIPTS -->
	<script>
	jQuery(document).ready(function() {     
	  Metronic.init(); // init metronic core components
	  Layout.init(); // init current layout
	  Demo.init();
	});

		
		jQuery('#forget-password').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.forget-form').show();
	        });

	    jQuery('#register-btn').click(function () {
	            jQuery('.login-form').hide();
	            jQuery('.register-form').show();
	        });
			
	</script>

<?php echo $script; ?>	
	
	<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>