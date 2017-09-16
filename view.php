<?php 
	//pulls all required and global information
	include_once 'required.php';
	//only load the page if someone is actually logged in
	//echo ini_get('post_max_size');
  	//echo ini_get('upload_max_filesize');
	
	$sceen_helper = new Screen();
		
	//load start session for storage
	if(isset($_GET['id'])) {
		$screen = $sceen_helper->getScreenByRef($_GET['id']);
		if($screen['content_id']) {
			$content = new Content($screen['content_id']);
		} else {
			$content = '';	
		}
	} else {
		$content = '';	
	} 
?>


<!DOCTYPE html>
<html>
<head>
	<title>
	<?php if(!$content) { ?>
	<?php } else { ?>
		<?php echo $content->content_title; ?>
	<?php } ?>	
	</title>
	<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<style>
	body {
		margin:0px;
	}
	
	#content_screen {
		width:100%;
		height:100%;
		background-color:black;
		color:white;
	}
	
	#content_screen img {
		width:auto;
		height:100%;
	}
	</style>
</head>
<body>
<div id="content_screen">
	<?php if(!$content) { ?>
		There is no content - so we should run adds or something		
	<?php } else { ?>
		<img src='<?php echo $content->content_image; ?>' />
	<?php } ?>	
</div>

<script type="text/javascript">
    setTimeout(function () { 
      location.reload();
    }, 180 * 1000);
    //location.reload();
    
    window.onload = maxWindow;

    function maxWindow() {
        window.moveTo(0, 0);


        if (document.all) {
            top.window.resizeTo(screen.availWidth, screen.availHeight);
        }

        else if (document.layers || document.getElementById) {
            if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth) {
                top.window.outerHeight = screen.availHeight;
                top.window.outerWidth = screen.availWidth;
            }
        }
    }
</script>

</body>
</html>
