<?php

function signUpUser($json, $db_handle){
    $secret = $db_handle->secret;
    $data = json_decode($json);

    if(empty($data->email) || empty($data->phone) || empty($data->password) || empty($data->name) || empty($data->gender)){
        return $message = array(
            "message" => "details not present",
            "value" => false
        );
    }

    $email = addslashes($data->email);
    $phone = addslashes($data->phone);
    $password = md5($data->password);
   
    $name = addslashes($data->name);
    $gender = addslashes($data->gender);
    


        $findemail = "SELECT * FROM `logintable` WHERE `email` = '$email' AND `status` = 1";
        $emailresult = $db_handle->runQuery($findemail);
        if (!empty($emailresult)) {
            return $message = array(
                "message" => "already a user present with this email",
                "value" => false
            );
        }

       
       
        $loginquery = "INSERT INTO `logintable`(`email`,`password`,`name`,`gender`, `phone`) VALUES ('$email','$password','$name','$gender','$phone')";
        $loginresult = $db_handle->runInsertQuery($loginquery);
        if ($loginresult) {

                $tok[0]['exp'] = time() + 7257600;
                $tok[0]["type"] = "user";
                $tok[0]["userId"] = $loginresult;
                //$tok[0]['userEmail'] = $email;
                $jwt = jwtMaker($tok[0], $secret);
                $message = array(
                    "message" => "signup successfull",
                    "jwt" => $jwt,
                    "value" => true
                    
                );
                return $message;
            
    }else{
        $message = array(
            "message" => "unable to the add user",
            "value" => false
        );
        return $message;
    }
}



function loginUser($json, $db_handle) //login for user
{
    $data = json_decode($json);
    $secret = $db_handle->secret;
    if (!empty($data->email) && !empty($data->password)) {
        $email = addslashes($data->email);
        $password = md5($data->password);

        $sql = "SELECT * FROM `logintable` WHERE `email`='$email' AND `status` = 1"; //checking if user exists
        $msg = $db_handle->runQuery($sql);
        if ($msg) {
            $getpassword = $msg[0]['password'];
            if($password != $getpassword){
                return $message = array(
                    "message" => "email and password not matching",
                    "value" => false
                );
            }
            $uid = $msg[0]['userId'];
            $status = $msg[0]['status'];
            
            if($status == 0){
                return $message = array(
                    "message" => "you profile is blocked by the admin",
                    "value" => false
                );
            }
            
            $tok[0]['exp'] = time() + 7257600;
            if ($msg[0]['userType'] == 0) {
                $tok[0]["type"] = "admin";
                $tok[0]["userId"] = $uid;
                $tok[0]['userEmail'] = $email;
                $jwt = jwtMaker($tok[0], $secret);
                $message = array(
                    "message" => $jwt,
                    "userType" => 0,
                    "value" => true,
                );
                return $message;
            } else{
                $tok[0]["type"] = "user";
                $tok[0]["userId"] = $uid;
                $tok[0]['userEmail'] = $email;
                $jwt = jwtMaker($tok[0], $secret);
               
                    $message = array(
                        "message" => $jwt,
                        "userType" => 1,
                        "value" => true
                    );
                
                return $message;
            
        } 
    }else {
            $message = array(
                "message" => 'User not found',
                "value" => false
            );
            return $message;
        }

    } else {
        $message = array(
            "message" => 'Error Missing Parameters',
            "value" => false
        );
        return $message;
    }
}


?>
