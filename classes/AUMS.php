<?php
use GuzzleHttp\Client;
define('Authorization','Basic YWRtaW46YWRtaW5AQW5kQVBQ');
define('token','logintoken');
class AUMS{
    public static function handle($message, $from, $bot)
    {
        if($message=="ums"||$message=="/ums")
        {
            $reply="Send your AUMS credentials in this format\n\n`ums roll_number dob(yyyy-mm-dd)` \n\nExample \n\n`ums cb.en.u4cse17xxx 1990-08-31` ";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        }
        else if(sizeof(explode(" ", $message)) ==3){
            $credentials=substr($message, 4, strlen($message));
            $username=explode(" ", $credentials)[0];
            $dob=explode(" ", $credentials)[1];
            $ajaxURL = "https://amritavidya.amrita.edu:8444/DataServices/rest/authRes?rollno=".$username."&dob=".$dob."&user_type=Student";
            $headers = [
                'Authorization'=> constant('Authorization'),
                'token'=> constant('token'),
            ];
            $client = new Client([
                'headers'=>$headers
            ]);
            $response = $client->get($ajaxURL);
            if($response->getStatusCode()==200)
            {
                $data = json_decode($response->getBody());
                if($data->Status=="OK")
                {

                    $userData = array($from=>array('UserName'=>$username,'Name'=>$data->NAME , 'Email'=> $data->Email));
                    $writeToFile=json_encode($userData);
                    file_put_contents('userData.json', $writeToFile);
                    $reply="Hola ".$data->NAME."! Enter OTP to Continue \t ( /umsotp xxxxx )\n\nExample /umsotp 12345";
                    $bot->sendMessage($from, $reply, "markdown");
                }
                else{
                    $reply=$data->Status." ! \nTry Again...";
                    $bot->sendMessage($from, $reply, "markdown");
                    return;
                }

            }
            else{
                $reply="Something Went Wrong";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }

        }
        else if(sizeof(explode(" ", $message)) ==2){
            $credentials=substr($message, 7, strlen($message));
            $otp=explode(" ", $credentials)[0];
            $userData=file_get_contents('userData.json');
            $jsonData = json_decode($userData, true);
            $ajaxURL = "https://amritavidya.amrita.edu:8444/DataServices/rest/authRes/register?rollno=".$jsonData[$from]['UserName']."&otp=".$otp;
            $headers = [
                'Authorization'=> constant('Authorization'),
                'token'=> constant('token'),
            ];
            $client = new Client([
                'headers'=>$headers
            ]);
            $response = $client->get($ajaxURL);
            if($response->getStatusCode()==200)
            {
                $data = json_decode($response->getBody());

            }
            else{
                $reply="Something Went Wrong";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }
        }
        else {
            global $reply;
        }


    }
}

