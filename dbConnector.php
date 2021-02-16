<?php

	use MongoDB\Driver as Mongo;

	// Configuration
	$dbhost = 'localhost';
	$dbport = '27017';

    try{
        $mng = new Mongo\Manager("mongodb://$dbhost:$dbport");
            //Atlas connection example: new MongoDB\Driver\Manager("mongodb+srv://<db-username>:<db-password>@azure-westus-1-fzl9p.azure.mongodb.net/test?retryWrites=true&w=majority");
    }catch(Mongo\Exception\Exception $e){
        echo "Exception:", $e->getMessage(), "\n";
    }

?>