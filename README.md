# gas-queue-mgt

Queue Management Systems for LPG vendor agencies of Sri Lanka, for the LPG shortages in 2022

## Installation

### Requirements

* PHP 7.4 or later
* MariaDB 10.4 or later

### SMS Gateway

* By default, the system uses [FastSMS (Sri Lanka)](https://fastsms.lk/) service.
* If the service needs to be changed, `sendSMS()` function in `common/sms.php` needs to be changed.
* The configuration parameters in `common/config.php` needs to be changed accordingly.

### Steps

1. Clone code from GitHub<br>
   `git clone https://github.com/madhusankagoonathilake/gas-queue-mgt.git`
2. Move to the installation directory<br>
   `cd gas-queue-mgt/install`
3. Run the installation script. Enter necessary details when prompted.<br>
   `php install.php`

## (Near) Future Improvements

* **Tamil translation**
* Agency unregistering
* English translation
* Limit the queue for a configurable number of slots
* Reduce session timeout from the default PHP value
* Implement async SMS notifications or queued SMS notifications for issuing of gas cylinder batches 
* Implement IP & session level restriction to malicious activity
* Document [test cases](docs/TEST-CASES.md)

## Design Decisions

Following decisions were taken to reduce the time to deliver, wider understanding and minimize overheads

* Used flat PHP over OOP
* Minimized the use of external libraries and frameworks
