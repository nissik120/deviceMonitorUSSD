# Device Monitor USSD
An [AfricasTalking API](docs.africastalking.com/ussd) based sample USSD application for querying status of smart devices. Written in PHP and utilizing MongoDB database. 

## Description
### Database
The MongodB Database is used in this project, selected for its flexiblity and schema bucket pattern which is quite useful for IoT applications.
Three collections are created namely: Users, Devices and Sessions
* Users Collection: Contains details about the users the name, phone number and location details.
* Devices Collection: Has details about the device including the owner or user of the device and embedded document of readings from sensors.
* Sessions: Contains details about the current USSD session. This is key to ensuring the right response is show to the user of the USSD application.

### USSD Code Runthrough
* Upon receiving a POST from AT, the phone number is checked if existing in the database.
* If user does not exist the registration page is served up and user details queried from the user.
* Else if the user exists a check is done to see whether they have **no device**, **1 device** or **>1 device**. For each of these cases a specific menu is displayed.
* Invalid inputs from user are filtered and the design is that a notification raised that allows the user to return to the first menu.

## Deployment / Installation
* Setup a sandbox app and create a USSD channel that can be used for testing using AfricasTalking Simulator
* The callback URL used in creating the channel can be provided by using a web tunnelling service like Ngrok, if deploying on a local host or you can expose the application on a public url.
* For an official release of the USSD application the AfricasTalking website highlights the methodology applied.

## Authors
This project has been written and published by:
[Nissi Kazembe](https://www.linkedin.com/in/nissi-kazembe-394692117/)

## Version
* 0.1
  *Initial Release

## License
This project is licensed under the MIT Licence - see the LICENSE.md file for details.

## Acknowledgements
Inspiration and some code snippets i.e. patterns and structure used in this project.
*[steveokay](https://github.com/steveokay/AfricaStalking-Ussd-and-Airtime-App)
*[JaniKibichi](https://github.com/JaniKibichi/ussd-app-with-registration)
