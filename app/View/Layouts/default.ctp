<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		Past Paths
	</title>
	<?php 
		echo $this->Html->css('bootstrap/bootstrap');
		// echo $this->fetch('css');
		echo $this->Html->css('artefacts/scroll2');
		echo $this->Html->css('font-awesome.min');

		echo $this->Html->css('app');
	?>

	<?php
		echo $this->Html->meta('icon');

		// echo $this->Html->css('main');

		echo $this->fetch('meta');
		echo $this->fetch('script');
		echo $this->Html->script('d3.v3.min');
		echo $this->Html->script('mustache');
		echo $this->Html->script('jquery.min');
	?>
	
	<!-- <link href='http://fonts.googleapis.com/css?family=Alegreya' rel='stylesheet' type='text/css'> -->
</head>
<body>
<?php echo $this->fetch('content'); ?>
</body>
</html>
