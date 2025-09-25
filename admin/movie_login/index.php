<?php
require './vendor/autoload.php';

use Firebase\JWT\JWT;

$error = '';
<<<<<<< Updated upstream

if(isset($_POST["login"]))
{
	$connect = new PDO("mysql:host=localhost;dbname=school_cinema", "root", "root");

	if(empty($_POST["email"])){
		$error = 'Please Enter Email Details';
	} else if(empty($_POST["password"])){
		$error = 'Please Enter Password Details';
	} else {
		$query = "SELECT * FROM user WHERE user_email = ?";
		$statement = $connect->prepare($query);
		$statement->execute([$_POST["email"]]);
		$data = $statement->fetch(PDO::FETCH_ASSOC);
		if($data){
			if($data['user_password'] ===  $_POST['password']){
				$key = '1a3LM3W966D6QTJ5BJb9opunkUcw_d09NCOIJb9QZTsrneqOICoMoeYUDcd_NfaQyR787PAH98Vhue5g938jdkiyIZyJICytKlbjNBtebaHljIR6-zf3A2h3uy6pCtUFl1UhXWnV6madujY4_3SyUViRwBUOP-UudUL4wnJnKYUGDKsiZePPzBGrF4_gxJMRwF9lIWyUCHSh-PRGfvT7s1mu4-5ByYlFvGDQraP4ZiG5bC1TAKO_CnPyd1hrpdzBzNW4SfjqGKmz7IvLAHmRD-2AMQHpTU-hN2vwoA-iQxwQhfnqjM0nnwtZ0urE6HjKl6GWQW-KLnhtfw5n_84IRQ';
				$token = JWT::encode(
					array(
						'iat'		=>	time(),
						'nbf'		=>	time(),
						'exp'		=>	time() + 3600,
						'data'	=> array(
							'user_id'	=>	$data['user_id'],
							'user_name'	=>	$data['user_name']
						)
					),
					$key,
					'HS256'
				);
				setcookie("token", $token, time() + 3600, "/", "", true, true);
				header('location:../overzichtpagina/admin.php');

			} else {
				$error = 'Wrong Password';

			}
		} else {
			$error = 'Wrong Email Address';
		}
	}
}




=======
 
if(isset($_POST["login"])){ 
	$connect = new PDO("mysql:host=localhost;dbname=school_cinema", "root", "root");
 
    if (empty($_POST["email"])) {
        $error = 'Please Enter Email Details';
    } else if (empty($_POST["password"])) {
        $error = 'Please Enter Password Details';
    } else {
        $query = "SELECT * FROM user WHERE user_email = ?";
        $statement = $connect->prepare($query);
        $statement->execute([$_POST["email"]]);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            if ($data['user_password'] === $_POST['password']) {
                $key = '1a3LM3W966D6QTJ5BJb9opunkUcw_d09NCOIJb9QZTsrneqOICoMoeYUDcd_NfaQyR787PAH98Vhue5g938jdkiyIZyJICytKlbjNBtebaHljIR6-zf3A2h3uy6pCtUFl1UhXWnV6madujY4_3SyUViRwBUOP-UudUL4wnJnKYUGDKsiZePPzBGrF4_gxJMRwF9lIWyUCHSh-PRGfvT7s1mu4-5ByYlFvGDQraP4ZiG5bC1TAKO_CnPyd1hrpdzBzNW4SfjqGKmz7IvLAHmRD-2AMQHpTU-hN2vwoA-iQxwQhfnqjM0nnwtZ0urE6HjKl6GWQW-KLnhtfw5n_84IRQ';
                $token = JWT::encode(
                    array(
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + 3600,
                        'data' => array(
                            'user_id' => $data['user_id'],
                            'user_name' => $data['user_name']
                        )
                    ),
                    $key,
                    'HS256'
                );
                setcookie("token", $token, time() + 3600, "/", "", true, true);
                header('location:../overzichtpagina/admin.php');
 
            } else {
                $error = 'Wrong Password';
 
            }
        } else {
            $error = 'Wrong Email Address';
        }
   
    }
}
 
 
 
 
>>>>>>> Stashed changes
?>


<!doctype html>
<html lang="en">
  	<head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">

    	<!-- Bootstrap CSS -->
    	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css" class="stylesheet">

    	<title>Login</title>
  	</head>
  	<body>
    	<div class="container">
    		<h1 class="text-center mt-5 mb-5">Login</h1>
    		<div class="row">
    			<div class="col-md-4">&nbsp;</div>
    			<div class="col-md-4">
    				<?php

    				if($error !== '')
    				{
    					echo '<div class="alert alert-danger">'.$error.'</div>';
    				}

    				?>
		    		<div class="card">
					<div class="card-header" style="background-color: #ff8a9d3d;">Login</div>
		    			<div class="card-body">
		    				<form method="post">
		    					<div class="mb-3">
			    					<label>Email</label>
			    					<input type="email" name="email" class="form-control" />
			    				</div>
			    				<div class="mb-3">
			    					<label>Wachtwoord</label>
			    					<input type="password" name="password" class="form-control" />
			    				</div>
			    				<div class="text-center">
			    					<input type="submit" name="login" class="login-button" value="Login" style="color:white;" />
			    				</div>
		    				</form>
		    			</div>
		    		</div>
		    	</div>
	    	</div>
    	</div>
  	</body>
</html>
