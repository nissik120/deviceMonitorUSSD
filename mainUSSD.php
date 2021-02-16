<?php

use MongoDB\Driver as Mongo;

if(!empty($_POST)){
    
    require_once('dbConnector.php');

    //variables from Gateway : *384*67468#
    $sessionId = $_POST["sessionId"];
    $serviceCode = $_POST["serviceCode"];
    $phoneNumber = ltrim($_POST["phoneNumber"], "+");
    $text = $_POST["text"];
    

    //get response
    $textArray = explode("*",$text);
    $userResponse = trim(end($textArray));

    //set default level and device
    $level = 0;
    $device = 0;


    //get level of user from db or retain if not found
    $sessionFilter = ['sessionId'=>$sessionId];
    $sessionQuery = new Mongo\Query($sessionFilter);
    $sessionRes = $mng->executeQuery("ussdsmartdb.sessions", $sessionQuery);
    if($userLevel = current($sessionRes->toArray())){    
        $level = $userLevel->level;
        $device = $userLevel->device;
    }
    
    //check if user available
    $userFilter = [
        'address.district'=>array('$ne'=>''),
        '$and'=>array(['phoneNumber'=>$phoneNumber])
    ];
    $userQuery = new Mongo\Query($userFilter);

    $userRes = $mng->executeQuery("ussdsmartdb.users", $userQuery);
    $userAvailable = current($userRes->toArray());

    if($userAvailable){

        $devUserFilter = ['device_user'=>$phoneNumber];
        $devUserQuery = new Mongo\Query($devUserFilter);
        $devUserRes = $mng->executeQuery("ussdsmartdb.devices", $devUserQuery);

        $userDevices = $devUserRes->toArray();

        if(!empty($userDevices)){
            
            switch($userResponse){

                case "":
                    if(count($userDevices)>1 && $level==0){

                        //change users session level
                        $levelUpsertBulk = new Mongo\BulkWrite();
                        $levelUpsertBulk->update(
                            ['sessionId'=>$sessionId],
                            ['$set'=>['_id'=> new MongoDB\BSON\ObjectID, 'sessionId'=>$sessionId, 'phoneNumber'=>$phoneNumber,'level'=> 1, 'device'=>0]],
                            ['multi'=>false, 'upsert'=>true]
                        );
                        $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpsertBulk);

                        $response = "CON Welcome ".$userAvailable->userName.". Select your Smart Farms device\n";
                        //display device list
                        foreach($userDevices as $key=>$userDevice){
                            $response .= "".++$key.". {$userDevice->device_alias}\n";
                        }

                        header('Content-type: text/plain');
                        echo $response;

                    }elseif(count($userDevices)==1 && $level==0){
                        
                        //change users session level
                        $level2UpsertBulk = new Mongo\BulkWrite();
                        $level2UpsertBulk->update(
                            ['sessionId'=>$sessionId],
                            ['$set'=>['_id'=> new MongoDB\BSON\ObjectID, 'sessionId'=>$sessionId, 'phoneNumber'=>$phoneNumber,'level'=> 2, 'device'=>0]],
                            ['multi'=>false, 'upsert'=>true]
                        );
                        $mng->executeBulkWrite('ussdsmartdb.sessions', $level2UpsertBulk);

                        //Serve user menu
                        $response = "CON Welcome ".$userAvailable->userName.". Get status on:\n";
                        $response .= "0. Get Summary\n";
                        $response .= "1. Humidity\n";
                        $response .= "2. Room Temperature\n";
                        $response .= "3. Bed Temperature\n";
                        $response .= "4. Moisture\n";
                        $response .= "5. pH\n";

                        header('Content-type: text/plain');
                        echo $response;

                    }

                break;

                case "1";
                    if($level==2){
                        $response = "END Current Humidity is 2 mg/L.\n"; 
                        $response .= "* Sent to Inbox too.\n";

                        header('Content-type: text/plain');
                        echo $response;
                        break;
                    }
                
                case "2":
                    if($level==2){
                        $response = "END Current Room Temperature is 26 deg Celsius.\n"; 
                        $response .= "* Sent to Inbox too.\n";
    
                        header('Content-type: text/plain');
                        echo $response;
                        break;    
                    }


                case "3":
                    if($level==2){
                        $response = "END Current Bed Temperature is 25 deg Celsius.\n"; 
                        $response .= "* Sent to Inbox too.\n";
    
                        header('Content-type: text/plain');
                        echo $response;
                        break;    
                    }  


                case "4":
                    if($level==2){
                        $response = "END Current Moisture is 22mg/L.\n"; 
                        $response .= "* Sent to Inbox too.\n";
    
                        header('Content-type: text/plain');
                        echo $response;
                        break;    
                    }    


                case "5":
                    if($level==2){
                        $response = "END Current Soil pH is 6.5.\n"; 
                        $response .= "* Sent to Inbox too.\n";
    
                        header('Content-type: text/plain');
                        echo $response;
                        break;    
                    }


                case "9":
                    if($level==-1){

                        if(count($userDevices)>1){

                            //change users session level
                            $levelUpdateBulk = new Mongo\BulkWrite();
                            $levelUpdateBulk->update(
                                ['sessionId'=>$sessionId],
                                ['$set'=>['level'=>1]],
                                ['multi'=>false, 'upsert'=>false]
                            );
                            $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);
    
                            $response = "CON Welcome ".$userAvailable->userName.". Select your Smart Farms device\n";
                            //display device list
                            foreach($userDevices as $key=>$userDevice){
                                $response .= "".++$key.". {$userDevice->device_alias}\n";
                            }
    
                            header('Content-type: text/plain');
                            echo $response;
    
                        }elseif(count($userDevices)==1){
                            
                            //change users session level
                            $levelUpdateBulk = new Mongo\BulkWrite();
                            $levelUpdateBulk->update(
                                ['sessionId'=>$sessionId],
                                ['$set'=>['level'=>2]],
                                ['multi'=>false, 'upsert'=>false]
                            );
                            $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);
    
                            //Serve user menu
                            $response = "CON Welcome ".$userAvailable->userName.". Get status on:\n";
                            $response .= "0. Get Summary\n";
                            $response .= "1. Humidity\n";
                            $response .= "2. Room Temperature\n";
                            $response .= "3. Bed Temperature\n";
                            $response .= "4. Moisture\n";
                            $response .= "5. pH\n";
    
                            header('Content-type: text/plain');
                            echo $response;
    
                        }                
                        break;
                    }


                default:

                    if($level==1){
                        $response = "CON Smart Farms Level 1\n";
                        $respCheck = intval($userResponse);
                        if($respCheck>0 && $respCheck<=count($userDevices)){

                            //update level
                            $levelUpdateBulk = new Mongo\BulkWrite();
                            $levelUpdateBulk->update(
                                ['sessionId'=>$sessionId],
                                ['$set'=>['level'=> 2, 'device'=>$respCheck]],
                                ['multi'=>false, 'upsert'=>false]
                            );
                            $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);

                            //Serve user menu"+$userDevices[$respCheck]->device_alias+"
                            $deviceName =($userDevices[--$respCheck])->device_alias;
                            $response .= "Get status on {$deviceName}:\n";
                            $response .= "0. Get Summary\n";
                            $response .= "1. Humidity\n";
                            $response .= "2. Room Temperature\n";
                            $response .= "3. Bed Temperature\n";
                            $response .= "4. Moisture\n";
                            $response .= "5. pH\n";

                            header('Content-type: text/plain');
                            echo $response;

                        }else{
                            //update level
                            $levelUpdateBulk = new Mongo\BulkWrite();
                            $levelUpdateBulk->update(
                                ['sessionId'=>$sessionId],
                                ['$set'=>['level'=>-1]],
                                ['multi'=>false, 'upsert'=>false]
                            );
                            $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);

                            $response .= "Input a valid device selection.\n";
                            $response .= "Enter 9 to go back to main menu\n";

                            header('Content-type: text/plain');
                            echo $response;
                        }

                    }elseif($level==2){

                            $levelUpdateBulk = new Mongo\BulkWrite();
                            $levelUpdateBulk->update(
                                ['sessionId'=>$sessionId],
                                ['$set'=>['level'=>-1]],
                                ['multi'=>false, 'upsert'=>false]
                            );
                            $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);

                            $response ="CON Input a valid selection.\n";
                            $response .="Enter 9 to go back to main menu\n";

                            header('Content-type: text/plain');
                            echo $response;

                    }
                    else{

                        $response ="CON Invalid input.\n";
                        $response .="Please Enter 9 to go back main menu\n";

                        header('Content-type: text/plain');
                        echo $response;
                    }
                    
                    break;

            }

        }else{

            $response = "END Hello ".$userAvailable->userName.".\n";
            $response .= "You have no registered or active devices\n";
            $response .= "Contact us if this is an error.\n";


            header('Content-type: text/plain');
            echo $response;

        }

    }else{

        //REGISTRATION

        if($userResponse ==""){

            switch($level){

                case 0:

                    //change users session level
                    $levelBulk = new Mongo\BulkWrite();
                    $sessionDoc = ['_id'=> new MongoDB\BSON\ObjectID, 'sessionId'=>$sessionId, 'phoneNumber'=>$phoneNumber, 'level'=> 1]; 
                    $levelBulk->insert($sessionDoc);
                    $mng->executeBulkWrite('ussdsmartdb.sessions', $levelBulk);

                    $response = "CON New User, Register With Us... \n";
                    $response .= "Please enter your name\n";

                    header('Content-type: text/plain');
                    echo $response;                    

                break;

                case 1:
                    //Request for username again
                    $response ="CON Name can not be empty. Please enter your name \n";

                    header('Content-type: text/plain');
                    echo $response;                    

                break;

                case 2:

                    //Request again for product code
                    $response ="CON District can not be empty. Please enter your district \n";

                    header('Content-type: text/plain');
                    echo $response;                    

                break;

                default:
                    $response ="END Apologies, encountered an error. \n";

                    header('Content-type: text/plain');
                    echo $response; 
                break;
                
            }

        }else{

            switch($level){

                case 0:

                    $response ="END This level can not be shown.\n";
                    header('Content-type: text/plain');
                    echo $response; 

                break;

                case 1:
                    //insert user details
                    $userBulk = new Mongo\BulkWrite();
                    $userBulk->update(
                        ['phoneNumber'=>$phoneNumber],
                        ['$set'=>array("_id"=> new MongoDB\BSON\ObjectID, "userName"=>$userResponse, "phoneNumber"=>$phoneNumber, "address"=>(object)array("area"=>"", "district"=>""))],
                        ['multi'=>false, 'upsert'=>true]
                    );
                    $mng->executeBulkWrite('ussdsmartdb.users', $userBulk);

                    //update level
                    $levelUpdateBulk = new Mongo\BulkWrite();
                    $levelUpdateBulk->update(
                        ['sessionId'=>$sessionId],
                        ['$set'=>['level'=> 2]],
                        ['multi'=>false, 'upsert'=>false]
                    );
                    $mng->executeBulkWrite('ussdsmartdb.sessions', $levelUpdateBulk);

                    //Request for district
                    $response ="CON Please enter your district. \n";

                    header('Content-type: text/plain');
                    echo $response;
                break;

                case 2:
                    //update district
                    $userUpdateBulk = new Mongo\BulkWrite();
                    $userUpdateBulk->update(
                        ["phoneNumber"=>$phoneNumber], 
                        ['$set'=>['address'=>(object)array('area'=>"", 'district'=>$userResponse)]],
                        ['multi'=>false, 'upsert'=>false]
                    );
                    $mng->executeBulkWrite('ussdsmartdb.users', $userUpdateBulk);

                    //show welcome message
                    $response ="END Thank you for registering to the Smart Farms Community.\n";
                    $response .= "Check back in 5 minutes to enjoy our services.";

                    header('Content-type: text/plain');
                    echo $response;
                break;

                default:

                    $response ="END Apologies, encountered an error. \n";
                    header('Content-type: text/plain');
                    echo $response;

                break;

            }

        }



    }
    
}

?>