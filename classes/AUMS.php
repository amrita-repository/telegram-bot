<?php
use GuzzleHttp\Client;

class AUMS
{
    public static function handle($message, $from, $bot)
    {
        $repo = new AUMSRepository();
        if ($message == "ums" || $message == "/ums") {
            $reply = "Send your AUMS credentials in this format\n\n`ums roll_number dob(yyyy-mm-dd)` \n\nExample \n\n`ums cb.en.u4cse17xxx 1990-08-31` ";
            $bot->sendMessage($from, $reply, "markdown");
            return;
        } else if (sizeof(explode(" ", $message)) == 3 and ((explode(" ", $message)[0] == "ums") || (explode(" ", $message)[0] == "/ums"))) {
            $credentials = substr($message, 4, strlen($message));
            $username = explode(" ", $credentials)[0];
            $dob = explode(" ", $credentials)[1];
            return $repo->getUser($from, $username, $dob);

//            $ajaxURL = "https://amritavidya.amrita.edu:8444/DataServices/rest/authRes?rollno=".$username."&dob=".$dob."&user_type=Student";
//            $headers = [
//                'Authorization'=> AUTHORIZATION,
//                'token'=> TOKEN,
//            ];
//            $client = new Client([
//                'headers'=>$headers
//            ]);
//            $response = $client->get($ajaxURL);
//            if($response->getStatusCode()==200)
//            {
//                $data = json_decode($response->getBody());
//                if($data->Status=="OK")
//                {
//
//                    $userData = array($from=>array('UserName'=>$username,'Name'=>$data->NAME , 'Email'=> $data->Email));
//                    $writeToFile=json_encode($userData);
//                    file_put_contents('userData.json', $writeToFile);
//                    $reply="Hola ".$data->NAME."! Enter OTP to Continue \t ( /umsotp xxxxx )\n\nExample /umsotp 12345";
//                    $bot->sendMessage($from, $reply, "markdown");
//                }
//                else{
//                    $reply=$data->Status." ! \nTry Again...";
//                    $bot->sendMessage($from, $reply, "markdown");
//                    return;
//                }
//
//            }
//            else{
//                $reply="Something Went Wrong";
//                $bot->sendMessage($from, $reply, "markdown");
//                return;
//            }
//
        } else if (sizeof(explode(" ", $message)) == 2 and (explode(" ", $message)[0] == "/umsotp")) {
            $otp = explode(" ", $message)[1];
            $userData = file_get_contents('userData.json');
            $jsonData = json_decode($userData, true);
            $ajaxURL = "https://amritavidya.amrita.edu:8444/DataServices/rest/authRes/register?rollno=" . $jsonData[$from]['UserName'] . "&otp=" . $otp;
            $headers = [
                'Authorization' => constant('AUTHORIZATION'),
                'token' => constant('TOKEN'),
            ];
            $client = new Client([
                'headers' => $headers
            ]);
            $response = $client->get($ajaxURL);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody());
                if ($data->Status == "Y") {
                    $userData = file_get_contents('userData.json');
                    $jsonData = json_decode($userData, true);
                    $jsonData[$from]['Token'] = $data->Token;
                    file_put_contents('userData.json', json_encode($jsonData));
                    $reply = "Hurray! Login Successful \nFor Attendance /ums_a\nFor Grades /ums_g";
                    $bot->sendMessage($from, $reply, "markdown");
                    $ajaxURL = "https://amritavidya.amrita.edu:8444/DataServices/rest/authRes/register?rollno=" . $jsonData[$from]['UserName'];
                    $headers = [
                        'Authorization' => AUTHORIZATION,
                        'token' => $jsonData[$from]['Token']
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $response = $client->get($ajaxURL);
                } else {
                    $reply = "Incorrect OTP...";
                    $bot->sendMessage($from, $reply, "markdown");
                    return;
                }
            } else {
                $reply = "Oh, the request failed.";
                $bot->sendMessage($from, $reply, "markdown");
                return;
            }
        } else {
            global $reply;
        }


    }
}

