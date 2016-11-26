<form action="ApiCooperons.php" method="POST">
	Api Key <input type="text" name="apikey" id="apikey" value="MzgyZDY5Mjg0MmNjZTIzYjdjMWJlYTBmZTg3NTQ1NmE5MmQ4MDA0Mw"/>
	<br />
	MemberProgram ID <input type="text" name="memberId" id="memberId" />
	<br />
	token Invitation <input type="text" name="token" id="token" />
	<br />
	Opération <select name="operation" id="operation>">
				<option value="member">Member</option>
				<option value="points">Points</option>
				<option value="invitation">Invitation</option>
			  </select>
	<br />		  
	Méthode <select name="methode" id="methode" >
			<option value="get">GET</option>
			<option value="post">POST</option>
			<option value="put">PUT</option>
			</select>
	<br />
    <select name="mailInvitationSet" id="mailInvitationSet" >
        <option value="0">------</option>
        <option value="1">Envoyer Data</option>
    </select>
    Mail Invitation <input type="text" name="mailInvitation" id="mailInvitation" />
    <br />
    <select name="sendMail" id="sendMail" >
        <option value="0">------</option>
        <option value="1">Envoyer Mail</option>
    </select>
    <br />
	<hr />
	<br />
	<hr />
	<div style="color:green;">
		[=== POST Member OU Invitation === ]<br />
		FirstName <input type="text" name="firstName" id="firstName" />
		<br />
		LastName <input type="text" name="lastName" id="lastName" />
		<br />
		Email <input type="text" name="email" id="email" />
		<br />
		[=== END POST Member OU Invitation ===]
	</div>
	<br />
	<hr />
	<div style="color:blue;">
		[=== POST POINTS === ]<br />
		Label opération <input type="text" name="labelOperation" id="labelOperation" />
		<br />
		Amount <input type="text" name="amount" id="amount" />
		<br />
		Info <input type="text" name="info" id="info" />
		<br />
		[=== END POST POINTS ===]
	</div>
	<br />
	<hr />
	<input type="submit" value="Valider" />		
</form>
<?php
if($_POST){
    // PREPROD:
    //$url  = "http://preprod.cooperons.com/app_dev.php/api/";
	// DEV:
	 $url  = "http://dev.plus/app_dev.php/api/";
	// GET
	if(strtoupper($_POST['methode']) == 'GET'){
		if(strtoupper($_POST['operation']) == 'MEMBER'){
			$url .= "members/".$_POST['memberId'].".json";
		}elseif(strtoupper($_POST['operation']) == 'INVITATION'){
			$url .= "invitations/".$_POST['token'].".json";
		}

		$result = json_decode(apiAppel($url, 'GET', $_POST['apikey']));
		echo '<pre>';
		print_r($result);
		exit;
	}
	// PUT
	if(strtoupper($_POST['methode']) == 'PUT'){
		if(strtoupper($_POST['operation']) == 'MEMBER'){
			$dataPost = array();
            if($_POST['mailInvitationSet'] == 1) $dataPost['mailInvitation'] = $_POST['mailInvitation'];
			$url .= "members/".$_POST['memberId'].".json";
		}
		$result = json_decode(apiAppel($url, 'PUT', $_POST['apikey'], json_encode($dataPost)));
		echo '<pre>';
		print_r($result);
		exit;
	}
    // POST
    if(strtoupper($_POST['methode']) == 'POST'){
		if(strtoupper($_POST['operation']) == 'MEMBER'){
			$dataPost = array(
				'userId' => $_POST['memberId'],
				'email' => $_POST['email'],
				'firstName' => utf8_encode($_POST['firstName']),
				'lastName' => utf8_encode($_POST['lastName']),
                'tokenInvitation' => $_POST['token']
			);
            $url .= "members.json";
			$result = json_decode(apiAppel($url, 'POST', $_POST['apikey'], json_encode($dataPost)));
			echo '<pre>';
			print_r($result);
			exit;
		}elseif(strtoupper($_POST['operation']) == 'POINTS'){
			$dataPost = array(
				'amount'     => $_POST['amount'],
				'labelOperation' => utf8_encode($_POST['labelOperation']),
				'info'  => utf8_encode($_POST['info']),
			);
			$url .= "members/".$_POST['memberId']."/points.json";
			$result = json_decode(apiAppel($url, 'POST', $_POST['apikey'], json_encode($dataPost)));
			echo '<pre>';
			print_r($result);
			exit;
		}elseif(strtoupper($_POST['operation']) == 'INVITATION'){
			$dataPost = array(
				'email' => $_POST['email'],
                'firstName' => utf8_encode($_POST['firstName']),
                'lastName' => utf8_encode($_POST['lastName']),
                'sendMail' => $dataPost['sendMail'] = $_POST['sendMail']?true:false
			);
            if($_POST['mailInvitationSet'] == 1) $dataPost['mailInvitation'] = $_POST['mailInvitation'];
            $url .= "invitations/".$_POST['memberId'].".json";
			$result = json_decode(apiAppel($url, 'POST', $_POST['apikey'], json_encode($dataPost)));
			echo '<pre>';
			print_r($result);
			exit;
		}
	}
 }
   
  function apiAppel($url, $method, $apikey, $data=null){
      if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
		}
		$apikey = ($apikey && $apikey != '')?$apikey:'NDYzNWM3NGY0YzA3NDRmYTVjZWUyNjQ0ZmYzMmQ3MjZiMzczYmUzZA';
	    $ch = curl_init();
	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_VERBOSE, 1);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:text/json;charset=UTF-8', 
			'Content-Length:'.strlen($data),
			'apikey:'.$apikey
		));
	  if(strtoupper($method) == 'POST' || strtoupper($method) == 'PUT'){
            if(strtoupper($method) == 'POST'){
                curl_setopt($ch,CURLOPT_POST, 1);
            }else{
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }
            curl_setopt($ch,CURLOPT_POSTFIELDS,  $data);
	  }
	  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
	  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
	  curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	  $response = curl_exec($ch);
      echo $response;
	  curl_close ($ch);
	  return $response;
  }
?>
