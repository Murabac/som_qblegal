		<?php
		$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
		foreach ($result as $row) 
		{
			$footer_about                = $row['footer_about'];
			$footer_copyright            = $row['footer_copyright'];
			$contact_address             = $row['contact_address'];
			$contact_email               = $row['contact_email'];
			$contact_phone               = $row['contact_phone'];
			$contact_fax                 = $row['contact_fax'];
			$total_recent_news_footer    = $row['total_recent_news_footer'];
			$total_popular_news_footer   = $row['total_popular_news_footer'];
			$total_recent_news_sidebar   = $row['total_recent_news_sidebar'];
			$total_popular_news_sidebar  = $row['total_popular_news_sidebar'];
			$total_recent_news_home_page = $row['total_recent_news_home_page'];
		}
		?>
		<!-- Footer Social Start -->
		<section class="footer-social">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="item">
							<ul>
								<?php
								// Getting and showing all the social media icon URL from the database
								$statement = $pdo->prepare("SELECT * FROM tbl_social");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
								foreach ($result as $row) 
								{
									if($row['social_url']!='')
									{
										echo '<li><a href="'.$row['social_url'].'"><i class="'.$row['social_icon'].'"style="margin-top: 10px"></i></a></li>';
									}
								}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Footer Social End -->

		
		<!-- Footer Main Start -->
		<section class="footer-main">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 col-md-4 col-lg-4 footer-col">
						<h3>About Us</h3>
						<p>
							<?php echo nl2br($footer_about); ?>
						</p>
					</div>
					<div class="col-sm-6 col-md-4 col-lg-4 footer-col">
						<h3>Latest News</h3>
						<?php
						$i=0;
						$statement = $pdo->prepare("SELECT * FROM tbl_news ORDER BY news_id DESC");
						$statement->execute();
						$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
						foreach ($result as $row) {
							$i++;
							if($i>$total_recent_news_footer) {break;}
							?>
							<div class="news-item">
								<div class="news-title"><a href="<?php echo BASE_URL; ?>news/<?php echo $row['news_slug']; ?>"><?php echo $row['news_title']; ?></a></div>
							</div>
							<?php
						}
						?>
					</div>
				
					<div class="col-sm-6 col-md-4 col-lg-4 footer-col">
						<h3>Contact Us</h3>
						<div class="contact-item">
							<div class="icon"><i class="fa fa-map-marker"></i></div>
							<div class="text"><?php echo $contact_address; ?></div>
						</div>
						<div class="contact-item">
							<div class="icon"><i class="fa fa-phone"></i></div>
							<div class="text"><?php echo $contact_phone; ?></div>
						</div>
						<div class="contact-item">
							<div class="icon"><i class="fa fa-fax"></i></div>
							<div class="text"><?php echo $contact_fax; ?></div>
						</div>
						<div class="contact-item">
							<div class="icon"><i class="fa fa-envelope-o"></i></div>
							<div class="text"><?php echo $contact_email; ?></div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- Footer Main End -->

		
		<!-- Footer Bottom Start -->
		<section class="footer-bottom">
			<div class="container">
				<div class="row">
					<div class="col-md-12 copyright" style="text-align: center;">
						Copyright © <span id="copyright">
        <script>document.getElementById('copyright').appendChild(document.createTextNode(new Date().getFullYear()))</script>
    </span>, QB Legal. All Rights Reserved.
					</div>
				</div>
			</div>
		</section>
		<!-- Footer Bottom End -->

		<a href="#" class="scrollup">
			<i class="fa fa-angle-up"></i>
		</a>

	</div>


	<!-- Scripts -->
	<script src="<?php echo BASE_URL; ?>assets/js/jquery-2.2.4.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/jquery.slicknav.min.js"></script>	
	<script src="<?php echo BASE_URL; ?>assets/js/hoverIntent.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/superfish.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/owl.carousel.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/owl.animate.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/wow.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/jquery.bxslider.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/jquery.mixitup.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/jquery.magnific-popup.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/toastr.min.js"></script>
	<script src="<?php echo BASE_URL; ?>assets/js/custom.js"></script>
	

	<script>
		toastr.options = {
			"closeButton": false,
			"debug": false,
			"newestOnTop": false,
			"progressBar": true,
			"positionClass": "toast-top-right",
			"preventDuplicates": true,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "3000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		}
	</script>

	<?php
        if( (isset($success_message)) && ($success_message!='') ):
            echo '
            <script>
            toastr.success(\''.$success_message.'\');
            </script>
            ';
        endif;
        if( (isset($error_message)) && ($error_message!='') ):
            echo '
            <script>
            toastr.error(\''.$error_message.'\');
            </script>
            ';
        endif;
    ?>
</body>
</html>