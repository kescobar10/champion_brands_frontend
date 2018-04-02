<!-- HTML top code goes here -->

<?

// From processing takes place here. message displayed here too
require('form/form.class.php');
$form = new Form();
$form->processForm(

	'championbrands.net' // Put you mail domain here
	,
	'Champion Brands' // Put your site name / form name here
	, 
	'info@championbrands.net' // Where will the form notification be sent?
	,
	'info@championbrands.net' // This what the form FROM: address wil be, if the form submissions doesn't contain an email field

);


?>

<!-- HTML bottom code goes here -->
