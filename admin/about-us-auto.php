<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

	if(empty($_POST['quote'])) {
		$valid = 0;
		$error_message .= 'Qoute can not be empty<br>';
	}

	if(empty($_POST['mission'])) {
		$valid = 0;
		$error_message .= 'Mission can not be empty<br>';
	}

	if(empty($_POST['vision'])) {
		$valid = 0;
		$error_message .= 'Vision can not be empty<br>';
	}


	if(empty($_POST['integrity'])) {
		$valid = 0;
		$error_message .= 'Integrity can not be empty<br>';
	}

	if(empty($_POST['collaboration'])) {
		$valid = 0;
		$error_message .= 'collaboration can not be empty<br>';
	}

	if(empty($_POST['client_focus'])) {
		$valid = 0;
		$error_message .= 'client_focus can not be empty<br>';
	}

	if(empty($_POST['gender_equality'])) {
		$valid = 0;
		$error_message .= 'gender_equality can not be empty<br>';
	}

	if(empty($_POST['execellence'])) {
		$valid = 0;
		$error_message .= 'execellence can not be empty<br>';
	}

	if(empty($_POST['professionalism'])) {
		$valid = 0;
		$error_message .= 'professionalism can not be empty<br>';
	}

	if(empty($_POST['precisions'])) {
		$valid = 0;
		$error_message .= 'precisions can not be empty<br>';
	}

	if(empty($_POST['promt'])) {
		$valid = 0;
		$error_message .= 'promt can not be empty<br>';
	}


	$path = $_FILES['photo']['name'];
    $path_tmp = $_FILES['photo']['tmp_name'];


    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    }
	

	if($valid == 1) {

		// getting auto increment id for photo renaming
		$statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_about_us_auto'");
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row) {
			$ai_id=$row[10];
		}

		if($_POST['news_slug'] == '') {
    		// generate slug
    		$temp_string = strtolower($_POST['quote']);
    		$news_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    	} else {
    		$temp_string = strtolower($_POST['news_slug']);
    		$news_slug = preg_replace('/[^A-Za-z0-9-]+/', '-', $temp_string);
    	}

    	// if slug already exists, then rename it
		$statement = $pdo->prepare("SELECT * FROM tbl_news WHERE news_slug=?");
		$statement->execute(array($news_slug));
		$total = $statement->rowCount();
		if($total) {
			$news_slug = $news_slug.'-1';
		}

		if($path=='') {
			// When no photo will be selected
			$statement = $pdo->prepare("UPDATE tbl_about_us_auto 
			SET 
			  quote = ?, 
			  mission = ?, 
			  vision = ?, 
			  integrity = ?, 
			  collaboration = ?, 
			  client_focus = ?, 
			  gender_equality = ?, 
			  execellence = ?, 
			  professionalism = ?, 
			  precisions = ?, 
			  promt = ? 
			WHERE id = 1;
			");
			$statement->execute(array($_POST['quote'],$_POST['mission'],$_POST['vision'],$_POST['integrity'],$_POST['collaboration'],$_POST['client_focus'],$_POST['gender_equality'],$_POST['execellence'],$_POST['professionalism'],$_POST['precisions'],$_POST['promt']));
		} else {
    		// uploading the photo into the main location and giving it a final name
    		$final_name = 'news-'.$ai_id.'.'.$ext;
            move_uploaded_file( $path_tmp, '../assets/uploads/'.$final_name );

            $statement = $pdo->prepare("UPDATE tbl_about_us_auto 
			SET 
			  quote = ?, 
			  mission = ?, 
			  vision = ?, 
			  integrity = ?, 
			  collaboration = ?, 
			  client_focus = ?, 
			  gender_equality = ?, 
			  execellence = ?, 
			  professionalism = ?, 
			  precisions = ?, 
			  promt = ? 
			WHERE id = ?;
			");
			$statement->execute(array($_POST['quote'],$_POST['mission'],$_POST['vision'],$_POST['integrity'],$_POST['collaboration'],$_POST['client_focus'],$_POST['gender_equality'],$_POST['execellence'],$_POST['professionalism'],$_POST['precisions'],$_POST['promt']));
		}
	
		$success_message = 'News is added successfully!';
	}
}
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_about_us_auto WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
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
		$photo = $row['photo'];

	}
?>
<section class="content-header">
	<div class="content-header-left">
		<h1>Edit About us Page</h1>
	</div>
</section>


<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
				<p>
				<?php echo $error_message; ?>
				</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
				<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">About Us Quote<span>*</span></label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="quote" placeholder="Example: News Headline" value="<?php echo $quote ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">News Slug </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="news_slug" placeholder="Example: news-headline">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Mission </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="mission" placeholder="Example: news-headline" value="<?php echo $mission ?>">
							</div>
						</div>
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">Vision </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="vision" placeholder="Example: news-headline" value="<?php echo $vision ?>">
							</div>
						</div>
                        <!-- Core Values Shit -->
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">INTEGRITY  </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="integrity" placeholder="Example: news-headline" value="<?php echo $integrity ?>">
							</div>
						</div>
                        
                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">COLLABORATION </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="collaboration" placeholder="Example: news-headline" value="<?php echo $collaboration ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">CLIENT FOCUS </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="client_focus" placeholder="Example: news-headline" value="<?php echo $client_focus ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">GENDER EQUALITY </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="gender_equality" placeholder="Example: news-headline" value="<?php echo $gender_equality ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">EXCELLENCE  </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="execellence" placeholder="Example: news-headline" value="<?php echo $execellence ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">PROFESSIONALISM  </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="professionalism" placeholder="Example: news-headline" value="<?php echo $professionalism ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">PRECISION  </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="precisions" placeholder="Example: news-headline" value="<?php echo $precisions ?>">
							</div>
						</div>

                        <div class="form-group">
							<label for="" class="col-sm-2 control-label">PROMPT  </label>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="promt" placeholder="Example: news-headline" value="<?php echo $promt ?>">
							</div>
						</div>


						<div class="form-group">
				            <label for="" class="col-sm-2 control-label">Featured Photo</label>
				            <div class="col-sm-6" style="padding-top:6px;">
				                <input type="file" name="photo">
				            </div>
				        </div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>