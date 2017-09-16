<?php 
 include __DIR__.'/../../../required.php';
 
 $video_id = preg_replace('/^.+?\/([^\/]+?)\/?$/', '$1', $_SERVER['REQUEST_URI']);
 $video = new Video($video_id);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $video->video_title; ?></title>
	<meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<style>
		@-ms-viewport { width: device-width; }
		@media only screen and (min-device-width: 800px) { html { overflow:hidden; } }
		html { height:100%; }
		body { height:100%; overflow:hidden; margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; background-color:#000000; }
	</style>
</head>
<body>
	
<script src="../../../viewer/embedpano.js"></script>

<div id="pano" style="width:100%;height:100%;">
	<noscript><table style="width:100%;height:100%;"><tr style="vertical-align:middle;"><td><div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div></td></tr></table></noscript>
	<script>
		embedpano({swf:"../../../viewer/krpano.swf", xml:"video.xml", target:"pano", html5:(document.domain ? "prefer" : "auto"), passQueryParameters:true});
	</script>
</div>

</body>
</html>