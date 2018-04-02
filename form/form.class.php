<?php

/*

	PHP form handler
	v1.1
	
	This is probably the simplest form processor you could ever imagine.
	
	created by InventPartners Ltd http://www.inventpartners.com

*/

class Form{

	var $mail_domain ,					// Mail domain used in notification emails
		$site_name ,					// Sitename used in message subject line
		$destination_email ,			// Destination email for notification emails
		$input_accepted ,				// Boolean: Has a form submission been sent?
		$includes_path ,				// Path to thankyou messgae includes, etc
		$default_include ,				// Default message to display when no form submission received
		$thankyou_include ,				// Thankyou message to display on this page
		$error_include ,				// Thankyou message to display on this page
		$filter_spam ,					// Boolean - Are we filtering out spam form submissions or not default is false;
		$spam_threshold	,				// Default is 5
		$formdata = array() ,			// _POST and _GET data goes into the array
		$required_fields = array() ,	// Required fields in form data
		$missing_required_fields = array() ; // Missing required fields data
		
	function Form(){
	
		global $_SERVER;
		
		// Try to work out the correct includes path
		$this->includes_path = 'form/';
		$this->default_include = 'default.inc.php';
		$this->thankyou_include = 'thankyou.inc.php';
		$this->error_include = 'error.inc.php';
		
		$this->filter_spam = false;
		$this->spam_threshold = 5;
	
	}
	
	// Call this function to run the form processor
	function processForm($mail_domain , $site_name , $destination_email , $sender_email){
		
		$this->mail_domain = $mail_domain;
		$this->site_name = $site_name;
		$this->destination_email = $destination_email;
		$this->sender_email = $sender_email;
		
		$this->input_accepted = 0;
	
		$this->required_fields = array();	
		$this->getInput($_GET);
		$this->getInput($_POST);
		
		// Has a form submission been accepted?
		if($this->input_accepted){
			//Check required_fields
			$passed=1;
			for($i=0; $i<count($this->required_fields); $i++){
				if(!$this->formdata[$this->required_fields[$i]]){
					$passed = 0;
					$this->missing_required_fields[] = array( 'id' => $this->required_fields[$i] ,
															  'title' => ucfirst(preg_replace("/_/" , ' ' , $this->required_fields[$i])) );
				}
			}
			// Has this form submission passed the required fields check?
			if($passed){
			
				// Form submission has pased the required fields check
				
				// Check for spam - 
				if($this->spamTrap()){
				
					// Send the notification email
					$this->sendNotification();
					// Display thank you message
					if(is_file($this->includes_path . $this->thankyou_include)){
						include($this->includes_path . $this->thankyou_include);
					} else {
						echo "<h1>CONFIGURATION ERROR</h1>\n";
						echo "<p>Can't find thank you message include.</p>";
						echo '<p>Form processor by <a href="http://www.inventpartners.com">Invent Partners <strong>Web Design Wakefield</strong></a>.</p>';
					}
					
				} else {
					// Spam will return a 404 to the page, and be quietly binned.
					header("HTTP/1.0 404 Not Found" , 1 , 1);
					$this->sendNotification('SPAM ');
					exit;
				}
			
			} else {
			
				// Missing required field
				// Error message goes here
				
				// Display error message
				if(is_file($this->includes_path . $this->error_include)){
					include($this->includes_path . $this->error_include);
				} else {
					echo "<h1>CONFIGURATION ERROR</h1>\n";
					echo "<p>can't find error message include.</p>";
					echo '<p>Form processor by <a href="http://www.inventpartners.com">Invent Partners <strong>Web Design Wakefield</strong></a>.</p>';
				}
			
			}
		} else {
		
			// No form submission to be had? 
			// Blank message
				
			// Display blank message
			if(is_file($this->includes_path . $this->default_include)){
				include($this->includes_path . $this->default_include);
			} else {
				echo "<h1>CONFIGURATION ERROR</h1>\n";
				echo "<p>can't find default message include.</p>";
				echo '<p>Form processor by <a href="http://www.inventpartners.com">Invent Partners <strong>Web Design Wakefield</strong></a>.</p>';
			}
			
		}
	
	}
	
	// Accept input data - send $_GET and $_POST to this data
	function getInput($input_array){
	
		while(list($key , $value) = each($input_array)){
			$this->input_accepted = 1;
			if(substr($key , 0 , 4) == 'req_'){
				$key = preg_replace("!req_!A" , '' , $key);
				$this->required_fields[] = $key;
			}
			if(get_magic_quotes_gpc()){
				$value = stripslashes($value);
			}
			$this->formdata[$key] = $value;
		}	
		
	}
	
	// Cehck valid email function
	function checkValidEmail($string) {
		if (preg_match("/^([a-zA-Z0-9\._-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/",$string)) {
			return true;
		} else {
			return false;
		}
	}
	
	// Send the form notification email
	function sendNotification($prefix = ''){
	
		// Build notification content
		$message = "The following enquiry has just been posted via the enquiry forms on " . $this->site_name .  "\n\n";
		while(list($key , $value) = each($this->formdata)){
			$message .= $key . ': ' . $value . "\n";
		}	

		// Are we changing the sender address
		if($this->checkValidEmail($this->formdata['email'])){
			$sender_email = $this->formdata['email'];
		} else {
			$sender_email = $this->sender_email;
		}
	
		// Send the email
		mail($this->destination_email , $prefix . 'Enquiry from ' . $this->site_name . ' website' , $message , 'From:' . $sender_email);
		
	}
	
	function spamTrap(){
	
		// Is spam filtering on?
		if($this->filter_spam){
			if(!$this->spam_threshold){
				$this->spam_threshold = 5;
			}
			
			// Check some junk indicators here
			$spam_score = 0;
			
			// Check for session values as well?
			// This eval here does not work, because the session has already been set!
			//if(session_id()){
			//	$spam_score -= 10;
			//}
			foreach($this->formdata as $key=>$value) {
				// Link for link spam hrefs, etc
				if(preg_match_all("!\[(link|url)=\S*\].*?\[/(\\1)\]!im" , $value , $spam_matches , PREG_SET_ORDER)){
					$spam_score += (count($spam_matches) * 3);
				}
				if(preg_match("!bcc:|cc:|\n|\r!i" , $value , $null)){
					$spam_score = $spamscore + 5;
				}
			}
			if($spam_score >= $this->spam_threshold){
				return 0;
			} else {
				return 1;
			}		
		} else {
			return 1;
		}
		
	}

}

?>