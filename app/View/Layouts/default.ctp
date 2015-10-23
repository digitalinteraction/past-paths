<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie9" lang="en"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="" lang="en"> <!--<![endif]-->

<!--[if lt IE 9]>
<?php
	echo $this->Html->script('html5shiv.js');
	echo $this->Html->script('respond.min.js');
?>
<![endif]-->

  <head>
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge;" />
	<?php echo $this->Html->charset(); ?>
	<title>
		Tyne & Wear Archives & Museums Collections Dive
	</title>
	<?php
		echo $this->Html->css('bootstrap/bootstrap');
		echo $this->Html->css('artefacts/bubble');
		echo $this->Html->css('font-awesome.min');
	?>

	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->script('withinviewport.js');

		if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']))
		{
		    // if IE<=8
		    echo $this->Html->script('jquery-1.11.3.min.js');
		}
		else
		{
		    // if IE>8 or not ie
		    echo $this->Html->script('jquery.min.js');
		}

		echo $this->fetch('meta');
		echo $this->fetch('script');
		echo $this->Html->script('google-analytics.js');
	?>

	<?php
	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$exploded_url = explode('/', $url);
	$s_id = end($exploded_url);
	?>

	<meta property="og:title" content="I discovered something amazing!" />
	<meta property="og:site_name" content="Tyne and Wear Museums & Archives"/>
	<meta property="og:url" content="<?php echo $url; ?>" />
	<meta property="og:description" content="Tyne and Wear Museums & Archives present a new discovery interface that immerses you in the collection. Explore the collection like never before using your mouse scroll speed to alter what you might find." />
	<meta property="fb:app_id" content="580833422019018" />

	<?php if($exploded_url[count($exploded_url) - 2] != 'share' && isset($artefact)):?>
		<!-- <meta property="og:image" content="<?php echo $url . $this->webroot . 'img/artefacts/large/' . $artefact['images'][0]['url']; ?>" /> -->
	<?php else: ?>
		<!-- <meta property="og:image" content="<?php echo $url . $this->webroot . 'img/artefacts/large/' . $artefact['images'][0]['url']; ?>" /> -->
	<?php endif; ?>
	<meta property="og:rich_attachment" content="true" />

	<script>
	var root_url = "<?php echo 'http://' . $_SERVER['HTTP_HOST']; ?>";

	$(document).ready(function() {

		  window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '580833422019018',
		      xfbml      : true,
		      version    : 'v2.3'
		    });
		  };

		  (function(d, s, id){
		     var js, fjs = d.getElementsByTagName(s)[0];
		     if (d.getElementById(id)) {return;}
		     js = d.createElement(s); js.id = id;
		     js.src = "//connect.facebook.net/en_US/sdk.js";
		     fjs.parentNode.insertBefore(js, fjs);
		   }(document, 'script', 'facebook-jssdk'));

	});
	</script>

	</head>
	<body>
		<?php echo $this->fetch('content'); ?>
	</body>
</html>
