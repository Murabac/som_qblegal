<?php
require_once('header.php');

// Preventing the direct access of this page.
if(!isset($_REQUEST['slug']))
{
	header('location: index.php');
	exit;
}
else
{
	// Check the page slug is valid or not.
	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=? AND status=?");
	$statement->execute(array($_REQUEST['slug'],'Active'));
	$total = $statement->rowCount();
	if( $total == 0 )
	{
		header('location: index.php');
		exit;
	}
}

// Getting the detailed data of a page from page slug
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE page_slug=?");
$statement->execute(array($_REQUEST['slug']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) 
{
	$page_name    = $row['page_name'];
	$page_slug    = $row['page_slug'];
	$page_content = $row['page_content'];
	$page_layout  = $row['page_layout'];
	$banner       = $row['banner'];
	$status       = $row['status'];
}

// If a page is not active, redirect the user while direct URL press
if($status == 'Inactive')
{
	header('location: index.php');
	exit;
}
?>
		
		
<!-- Banner Start -->
<div class="page-banner" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $banner; ?>)">
	<div class="overlay"></div>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="banner-text">
					<h1><?php echo $page_name; ?></h1>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Banner End -->


<?php if($page_layout == 'Full Width Page Layout'): ?>
<section class="about-v2">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php echo $page_content; ?>
				<section class="service-v1">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<!--<div class="heading">-->
				<!--	<h2>OUR SERVICES</h2>-->
				<!--	<p>We Are Here to Provide You Awesome Service Always</p>-->
				<!--</div>-->
			</div>
		</div>
		<div class="row">
			<?php
			$statement = $pdo->prepare("SELECT * FROM tbl_service ORDER BY id ASC");
			$statement->execute();
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result as $row) {
				?>
				<div class="col-sm-6 col-md-3 col-lg-3 s-item">
					<div class="item">
						<div class="photo" style="background-image:url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>);">
						</div>
						<div class="text">
							<div class="inner">
								<h3><a href="<?php echo BASE_URL; ?>service/<?php echo $row['slug']; ?>"><?php echo $row['name']; ?></a></h3>
								<p><?php echo substr($row['short_description'], 0, 60); ?>...</p>
								<p class="button">
									<a style = "color: white;" href="<?php echo BASE_URL; ?>service/<?php echo $row['slug']; ?>">Akhri wax dheeraad ah &raquo;</a>
								</p>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>


<?php if($page_layout == 'Contact Us Page Layout'): ?>
<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$contact_map_iframe = $row['contact_map_iframe'];
	}
?>
<section class="contact-v1">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="heading-normal">
					<h2>Contact Form</h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<div class="row">
					<div class="col-md-12" style="margin-bottom: 30px;">
						<h6 style="text-transform: uppercase; color:#999" class="text-center">Contact Us With Ease</h6>
						<h4 style="font-size: 40px;
							font-weight: bold;
							background-image: linear-gradient(to left, #553c9a, #b393d3);
							color: transparent;
							background-clip: text;
							-webkit-background-clip: text; 
							margin-top: 0px;" class="text-center">Get in touch</h4>
						<p style="font-weight: bold; color: #333; width: 80%; margin-left: 50px;" class="text-center">Come and visit our office or simply send us an email anytime you want we are open to any suggestions from our clients.</p>
					</div>
				</div>

<?php
// After form submit checking everything for email sending
if(isset($_POST['form_contact']))
{
	$error_message = '';
	$success_message = '';
	$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$send_email_from  = $row['send_email_from'];
		$receive_email_to = $row['receive_email_to'];
		$smtp_active      = $row['smtp_active'];
		$smtp_ssl         = $row['smtp_ssl'];
		$smtp_host        = $row['smtp_host'];
		$smtp_port        = $row['smtp_port'];
		$smtp_username    = $row['smtp_username'];
		$smtp_password    = $row['smtp_password'];

		$recaptcha_status = $row['recaptcha_status'];
	}

    $valid = 1;

    if(empty($_POST['visitor_name']))
    {
        $valid = 0;
        $error_message .= 'Please enter your name.<br>';
    }

    // if(empty($_POST['visitor_phone']))
    // {
    //     $valid = 0;
    //     $error_message .= 'Please enter your phone number.<br>';
    // }


    if(empty($_POST['visitor_email']))
    {
        $valid = 0;
        $error_message .= 'Please enter your email address.<br>';
    }
    else
    {
    	// Email validation check
        if(!filter_var($_POST['visitor_email'], FILTER_VALIDATE_EMAIL))
        {
            $valid = 0;
            $error_message .= 'Please enter a valid email address.<br>';
        }
    }

    if(empty($_POST['visitor_comment']))
    {
        $valid = 0;
        $error_message .= 'Please enter your comment.<br>';
    }

    if($recaptcha_status == 'On')
    {
	    if(empty($_POST['g-recaptcha-response'])) {
	    	$valid = 0;
	        $error_message .= 'Please check the the captcha form.<br>';
	    }	
    }
    

    if($valid == 1)
    {

    	$visitor_name = $_POST['visitor_name'];
    	$visitor_email = $_POST['visitor_email'];
    	$visitor_phone = $_POST['visitor_phone'];
    	$visitor_comment = $_POST['visitor_comment'];

        // sending email
		$message = '
<html><body>
<table>
<tr>
<td>Name</td>
<td>'.$visitor_name.'</td>
</tr>
<tr>
<td>Email</td>
<td>'.$visitor_email.'</td>
</tr>
<tr>
<td>Phone</td>
<td>'.$visitor_phone.'</td>
</tr>
<tr>
<td>Comment</td>
<td>'.nl2br($visitor_comment).'</td>
</tr>
</table>
</body></html>
';

		require_once('assets/mail/class.phpmailer.php');
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';

        if($smtp_active == 'Yes')
	    {
	    	if($smtp_ssl == 'Yes')
	    	{
	    		$mail->SMTPSecure = "ssl";
	    	}
	    	else
	    	{
	    		$mail->SMTPSecure = "tls";
	    	}
            $mail->IsSMTP();
			$mail->SMTPAuth = true;
			$mail->Host     = $smtp_host;
			$mail->Port     = $smtp_port;
			$mail->Username = $smtp_username;
			$mail->Password = $smtp_password;
        }

        $mail->addReplyTo($visitor_email);
	    $mail->setFrom($send_email_from);
	    $mail->addAddress($receive_email_to);
	    
	    $mail->isHTML(true);
	    $mail->Subject = 'Contact form email';

	    $mail->Body = $message;
	    $mail->send();
		
        $success_message = 'Thank you for sending the email. We will contact you shortly.';

    }
}
?>
				

				<form action="<?php echo BASE_URL; ?>page/<?php echo $_REQUEST['slug']; ?>" class="form-horizontal cform-1" method="post">
					<div class="form-group">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="Name" name="visitor_name">
                        </div>
                    </div>
					<div class="form-group">
                        <div class="col-sm-12">
                            <input type="email" class="form-control" placeholder="Email Address" name="visitor_email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" placeholder="Phone Number" name="visitor_phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <textarea name="visitor_comment" class="form-control" cols="30" rows="10" placeholder="Message"></textarea>
                        </div>
                    </div>
                    
                    <?php if($recaptcha_status == 'On'): ?>
					<div class="form-group">
						<div class="col-sm-12">
	                        <div class="g-recaptcha" data-sitekey="<?php echo $recaptcha_site_key; ?>"></div>
	                    </div>
                    </div>
                	<?php endif; ?>

                    <div class="form-group">
	                    <div class="col-sm-12">
	                        <input type="submit" value="Send" class="btn btn-success" name="form_contact">
	                    </div>
	                </div>
				</form>
			</div>
			<div class="col-md-5">
				<div class="row" style="margin-bottom: 10px;">
					<div class="col-md-4">
						<h4 style="font-size: 20px;
							font-weight: bold;
							background-image: linear-gradient(to left, #553c9a, #b393d3);
							color: transparent;
							background-clip: text;
							-webkit-background-clip: text; 
							margin-top: 0px;">Address</h4>
						<p style="font-weight: bold; color: #333;">Barsame B Canter, Wadada Timacade 26 June Hargeisa Somaliland</p>
					</div>
					<div class="col-md-4">
						<h4 style="font-size: 20px;
							font-weight: bold;
							background-image: linear-gradient(to left, #553c9a, #b393d3);
							color: transparent;
							background-clip: text;
							-webkit-background-clip: text; 
							margin-top: 0px;">Call Us</h4>
						<p style="font-weight: bold; color: #333;">+252 63 4749276</p>
						<p style="font-weight: bold; color: #333;">+252 63 4749276</p>
					</div>
					<div class="col-md-4">
						<h4 style="font-size: 20px;
							font-weight: bold;
							background-image: linear-gradient(to left, #553c9a, #b393d3);
							color: transparent;
							background-clip: text;
							-webkit-background-clip: text; 
							margin-top: 0px;">Email Us</h4>
						<p style="font-weight: bold; color: #333;">mur7bac@gmail.com</p>
					</div>
				</div>
				<div class="google-map">
					<?php echo $contact_map_iframe; ?>
				</div>	
			</div>
			
		</div>
	</div>
</section>
<?php endif; ?>


<style>

.row.equal-cols {
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-wrap: wrap;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;
}

.row.equal-cols:before,
.row.equal-cols:after {
  display: block;
}

.row.equal-cols > [class*='col-'] {
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-flex-direction: column;
  -ms-flex-direction: column;
  flex-direction: column;
}

.row.equal-cols > [class*='col-'] > * {
  -webkit-flex: 1 1 auto;
  -ms-flex: 1 1 auto;
  flex: 1 1 auto; 
}
</style>
<?php if($page_layout == 'FAQ Page Layout'): ?>

	<?php
	$statement = $pdo->prepare("SELECT * FROM tbl_about_us_auto WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) 
	{
		$quote = $row['quote'];
		$mission = $row['mission'];
		$vision = $row['vision'];
		$integrity = $row['integrity'];
		$collaboration = $row['collaboration'];
		$client_focus = $row['client_focus'];
		$gender_equality = $row['gender_equality'];
		$execellence = $row['execellence'];
		$professionalism = $row['professionalism'];
		$precisions = $row['precisions'];
		$promt = $row['promt'];


	}
?>
<section class="faq" style="padding-bottom: 0;">
	<div class="container">
			<div class="row justify-content-between "  style="margin-bottom: 5%">
			<div class="col-lg-4 d-flex align-items-center justify-content-center" style="background-color: #3f2a72; margin-top: 4%">
					<h1 class="" style="color: #fff; margin:0; padding: 5%; font-size: 18px; text-transform: none;"><?php echo $quote?></h1>
			</div>
			<div class="col-lg-8 pt-5 pt-lg-0 ">
				<div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 20px;">
					<i class="fa fa-bullseye" style="font-size:40px; font-weight: bold; margin-bottom: 5px; color:#3f2a72"></i>
					<h4 class="text-center" style="font-size:24px; font-weight: bold; margin-bottom: 15px; color:#3f2a72">MISSION</h4>
					<p class="align-items-center" style="font-size: 16px; text-align: justify; text-align-last: center;"><?php echo $mission?></p>
				</div> 
				<div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="200">
					<i class="fa fa-binoculars" style="font-size:40px; font-weight: bold; margin-bottom: 5px; color:#3f2a72"></i>
					<h4 class="text-center" style="font-size:24px; font-weight: bold; margin-bottom: 15px; color:#3f2a72"> VISION </h4>
					<p class="align-items-center" style="font-size: 16px; text-align: justify; text-align-last: center;"><?php echo $vision?></p>
				</div>
			</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
				<h4 class="text-center" style="font-size:26px; font-weight: bold;color:#3f2a72; text-transform:uppercase"> Core Values </h4>
				</div>
			</div>
			<div class="row equal-cols">
			<div class="col-lg-6 d-flex align-items-center justify-content-center" style="background-color: #3f2a72; padding: 10px 20px;">
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">INTEGRITY : </span><?php echo $integrity?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">COLLABORATION : </span><?php echo $collaboration?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">CLIENT FOCUS : </span><?php echo $client_focus?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">GENDER EQUALITY : </span><?php echo $gender_equality?></p>

			</div>
			<div class="col-lg-6 d-flex align-items-center justify-content-center" style="background-color: #533993; padding: 10px 20px;">
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">EXCELLENCE : </span><?php echo $execellence?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">PROFESSIONALISM : </span><?php echo $professionalism?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">PRECISION : </span><?php echo $precisions?></p>
				<p style="color:#fff; font-size: 16px;"><span style="font-weight: bold;">PROMPT : </span><?php echo $promt?></p>
			</div>
			</div>
			<div class="row">
				<div class="col-lg-12" style="padding: 0; margin-bottom: 20px">
				<div class="page-banner" style="background-image: url(<?php echo BASE_URL; ?>assets/uploads/legal-firn.jpg); height: 300px;">
				</div>
				</div>
			</div>
	</div>
</section>

<?php endif; ?>



<?php if($page_layout == 'Photo Gallery Page Layout'): ?>
<section class="gallery">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<ul class="gallery-menu">
					<li class="filter" data-filter="all" data-role="button">All</li>
					<?php
					$statement = $pdo->prepare("SELECT * FROM tbl_category_photo WHERE status=?");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$temp_string = strtolower($row['p_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    					?>
    					<li class="filter" data-filter=".<?php echo $temp_slug; ?>" data-role="button"><?php echo $row['p_category_name']; ?></li>
						<?php
					}
					?>
				</ul>

				<div id="mix-container">
					<?php
					$i=0;
					$statement = $pdo->prepare("SELECT
					                           	t1.photo_id,
												t1.photo_caption,
												t1.photo_name,
												t1.p_category_id,
												t2.p_category_id,
												t2.p_category_name,
												t2.status
					                            FROM tbl_photo t1
					                            JOIN tbl_category_photo t2
					                            ON t1.p_category_id = t2.p_category_id 
					                            ");
					$statement->execute();
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$i++;
						$temp_string = strtolower($row['p_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
						?>
						<div class="col-md-4 mix <?php echo $temp_slug; ?> all" data-my-order="<?php echo $i; ?>">
							<div class="inner">
								<div class="photo" style="background-image:url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo_name']; ?>);"></div>
								<div class="overlay"></div>
								<div class="icons">
									<div class="icons-inner">
										<a class="gallery-photo" href="<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo_name']; ?>"><i class="fa fa-search-plus"></i></a>
									</div>
								</div>
							</div>
						</div>
						<?php
					}
					?>

				</div>

			</div>
		</div>
	</div>
</section>
<?php endif; ?>





<?php if($page_layout == 'Video Gallery Page Layout'): ?>
<section class="gallery">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<ul class="gallery-menu">
					<li class="filter" data-filter="all" data-role="button">All</li>
					<?php
					$statement = $pdo->prepare("SELECT * FROM tbl_category_video WHERE status=?");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$temp_string = strtolower($row['v_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    					?>
    					<li class="filter" data-filter=".<?php echo $temp_slug; ?>" data-role="button"><?php echo $row['v_category_name']; ?></li>
						<?php
					}
					?>
				</ul>

				<div id="mix-container">
					<?php
					$i=0;
					$statement = $pdo->prepare("SELECT
					                           	t1.video_id,
												t1.video_title,
												t1.video_iframe,
												t1.v_category_id,
												t2.v_category_id,
												t2.v_category_name,
												t2.status
					                            FROM tbl_video t1
					                            JOIN tbl_category_video t2
					                            ON t1.v_category_id = t2.v_category_id 
					                            ");
					$statement->execute();
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						$i++;
						$temp_string = strtolower($row['v_category_name']);
    					$temp_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
						?>
						<div class="col-md-4 mix <?php echo $temp_slug; ?> all" data-my-order="<?php echo $i; ?>">
							<div class="inner viframe">
								<?php echo $row['video_iframe']; ?>
							</div>
						</div>
						<?php
					}
					?>

				</div>

			</div>
		</div>
	</div>
</section>
<?php endif; ?>



<?php if($page_layout == 'Blog Page Layout'): ?>
<section class="blog">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				
				<!-- Blog Classic Start -->
				<div class="blog-grid">
					<div class="row">
						<div class="col-md-12">
							

							<?php
							$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
							$statement->execute();
							$total = $statement->rowCount();
							?>

							<?php if(!$total): ?>
							<p style="color:red;">Sorry! No News is found.</p>
							<?php else: ?>




<?php
/* ===================== Pagination Code Starts ================== */
		$adjacents = 10;	
		
		$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
		$statement->execute();
		$total_pages = $statement->rowCount();
		
		$targetpage = $_SERVER['PHP_SELF'];
		$limit = 5;                                 
		$page = @$_GET['page'];
		if($page) 
			$start = ($page - 1) * $limit;          
		else
			$start = 0;	
		

		$statement = $pdo->prepare("SELECT
								   t1.news_title,
		                           t1.news_slug,
		                           t1.news_content,
		                           t1.news_date,
		                           t1.photo,
		                           t1.category_id,

		                           t2.category_id,
		                           t2.category_name,
		                           t2.category_slug
		                           FROM tbl_news t1
		                           JOIN tbl_category t2
		                           ON t1.category_id = t2.category_id 		                           
		                           ORDER BY t1.news_id 
		                           LIMIT $start, $limit");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		
		$s1 = $_REQUEST['slug'];
				
		if ($page == 0) $page = 1;                  
		$prev = $page - 1;                          
		$next = $page + 1;                          
		$lastpage = ceil($total_pages/$limit);      
		$lpm1 = $lastpage - 1;   
		$pagination = "";
		if($lastpage > 1)
		{   
			$pagination .= "<div class=\"pagination\">";
			if ($page > 1) 
				$pagination.= "<a href=\"$targetpage?slug=$s1&page=$prev\">&#171; previous</a>";
			else
				$pagination.= "<span class=\"disabled\">&#171; previous</span>";    
			if ($lastpage < 7 + ($adjacents * 2))   //not enough pages to bother breaking it up
			{   
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))    //enough pages to hide some
			{
				if($page < 1 + ($adjacents * 2))        
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
				}
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
					$pagination.= "...";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lpm1\">$lpm1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=$lastpage\">$lastpage</a>";       
				}
				else
				{
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=1\">1</a>";
					$pagination.= "<a href=\"$targetpage?slug=$s1&page=2\">2</a>";
					$pagination.= "...";
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= "<span class=\"current\">$counter</span>";
						else
							$pagination.= "<a href=\"$targetpage?slug=$s1&page=$counter\">$counter</a>";                 
					}
				}
			}
			if ($page < $counter - 1) 
				$pagination.= "<a href=\"$targetpage?slug=$s1&page=$next\">next &#187;</a>";
			else
				$pagination.= "<span class=\"disabled\">next &#187;</span>";
			$pagination.= "</div>\n";       
		}
		/* ===================== Pagination Code Ends ================== */
		?>

							<?php
							foreach ($result as $row) {
								?>
								<div class="post-item">
									<div class="image-holder">
										<img class="img-responsive" src="<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>" alt="<?php echo $row['news_title']; ?>">
									</div>
									<div class="text">
										<h3><a href="<?php echo BASE_URL; ?>news/<?php echo $row['news_slug']; ?>"><?php echo $row['news_title']; ?></a></h3>
										<ul class="status">
											<li><i class="fa fa-tag"></i>Category: <a href="<?php echo BASE_URL; ?>category/<?php echo $row['category_slug']; ?>"><?php echo $row['category_name']; ?></a></li>
											<li><i class="fa fa-calendar"></i>Date: <?php echo $row['news_date']; ?></li>
										</ul>
										<p>
											<?php echo substr($row['news_content'],0,200).' ...'; ?>
										</p>
										<p class="button">
											<a href="<?php echo BASE_URL; ?>news/<?php echo $row['news_slug']; ?>">Akhri wax dheeraad ah</a>
										</p>
									</div>
								</div>
								<?php
							}
							?>							
							<?php endif; ?>

						</div>

						<div class="col-md-12">
							<?php if($total): ?>
							<?php echo $pagination; ?>
							<?php endif; ?>
						</div>

					</div>
				</div>
				<!-- Blog Classic End -->

			</div>
			<div class="col-md-3">
				
				<?php require_once('sidebar.php'); ?>
			
			</div>

			


		</div>
	</div>
</section>
<?php endif; ?>



<?php if($page_layout == 'Attorney Page Layout'): ?>
<section class="attorney-v3">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<!-- Attorney Container Start -->
				<div class="attorney-inner">
					
					<?php
					$statement = $pdo->prepare("SELECT
												
												t1.id,
												t1.name,
												t1.slug,
												t1.designation_id,
												t1.photo,
												t1.degree,
												t1.detail,
												t1.facebook,
												t1.twitter,
												t1.linkedin,
												t1.youtube,
												t1.google_plus,
												t1.instagram,
												t1.flickr,
												t1.address,
												t1.practice_location,
												t1.phone, 
												t1.email,
												t1.website,
												t1.status,

												t2.designation_id,
												t2.designation_name
						
					                            FROM tbl_attorney t1
					                            JOIN tbl_designation t2
					                            ON t1.designation_id = t2.designation_id
					                            WHERE t1.status=?
					                            ORDER BY practice_location");
					$statement->execute(array('Active'));
					$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
					foreach ($result as $row) {
						?>
						<div class="col-md-3 item">
							<div class="inner">
								<div class="thumb">
									<div class="photo" style="background-image:url(<?php echo BASE_URL; ?>assets/uploads/<?php echo $row['photo']; ?>)"></div>
									<div class="overlay"></div>
									<div class="social-icons">
										<ul>
											<?php if($row['facebook']!=''): ?>
												<li><a href="<?php echo $row['facebook']; ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
											<?php endif; ?>

											<?php if($row['twitter']!=''): ?>
												<li><a href="<?php echo $row['twitter']; ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
											<?php endif; ?>

											<?php if($row['linkedin']!=''): ?>
												<li><a href="<?php echo $row['linkedin']; ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
											<?php endif; ?>

											<?php if($row['youtube']!=''): ?>
												<li><a href="<?php echo $row['youtube']; ?>" target="_blank"><i class="fa fa-youtube"></i></a></li>
											<?php endif; ?>

											<?php if($row['google_plus']!=''): ?>
												<li><a href="<?php echo $row['google_plus']; ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>
											<?php endif; ?>

											<?php if($row['instagram']!=''): ?>
												<li><a href="<?php echo $row['instagram']; ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>
											<?php endif; ?>

											<?php if($row['flickr']!=''): ?>
												<li><a href="<?php echo $row['flickr']; ?>" target="_blank"><i class="fa fa-flickr"></i></a></li>
											<?php endif; ?>
										</ul>
									</div>
								</div>
								<div class="text">
									<h3><a href="<?php echo BASE_URL; ?>attorney/<?php echo $row['slug']; ?>"><?php echo $row['name']; ?></a></h3>
									<h4><?php echo $row['designation_name']; ?></h4>
									<p class="button">
										<a href="<?php echo BASE_URL; ?>attorney/<?php echo $row['slug']; ?>">See Full Profile</a>
									</p>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					
				</div>
				<!-- Attorney Container End -->

			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php require_once('footer.php'); ?>