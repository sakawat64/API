<?php
require('routeros_api.class.php');
    
class Mikrotik extends RouterosAPI{

    private $connectStatus = false;
    
    public function __construct($ip, $name, $password) {
        if($this->connect($ip, $name, $password)){
            $this->connectStatus = true;
        }
    }
    
    public function checkConnection(){
        return $this->connectStatus;
    }

    public function checkSecretActive($secretName) {

        $this ->write("/ppp/secret/print",false);
        $this ->write("?name=".$secretName);
        $READ = $this->read(false);
        $singleSecretData = $this->parseResponse($READ);
        
        if (isset($singleSecretData[0]['disabled'])) {

           if ($singleSecretData[0]['disabled'] == 'false') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function showSingleSecret($secretName) {

        $this ->write("/ppp/secret/print",false);
        $this ->write("?name=".$secretName);
        $READ = $this->read(false);
        $singleSecretData = $this->parseResponse($READ);
        if(!empty($singleSecretData)){
            return $singleSecretData[0];
        }else{
            return null;
        }
    }

    public function viewAllPppSecret(){
        
        $this->write('/ppp/secret/print');
        $READ = $this->read(false);
        $allData = $this->parseResponse($READ);
        return $allData;
    }
    
    public function monitorTrafic(){
        
        $this->write('/interface/monitor-traffic', FALSE);
        $this->write('=interface=LAN',false);
        $this->write('=once=');
        $READ = $this->read(false);
        $traficArray = $this->parseResponse($READ);
        return $traficArray;
    }

    
    public function interfaceStatus(){
        
        $this->write('/interface/print', true);
        $READ = $this->read(false);
        $interface = $this->parseResponse($READ);
        return $interface;
    }

    
    public function profileStatus(){
        
        $this->write('/ppp/profile/print', true);
        $READ = $this->read(false);
        $profile = $this->parseResponse($READ);
        return $profile;
    }

    
    public function enableSingleSecret($userName) {
        
        $this->comm("/ppp/secret/enable", array(
            "numbers" => $userName,
        ));
    }

    
    public function disableSingleSecret($userName){
        
        $this->comm("/ppp/secret/disable", array(
            "numbers" => $userName,
        ));
    }

    
    public function createNewSecret($user, $password, $service='pppoe', $profile){
        $this->comm("/ppp/secret/add", array(
            "name" => $user,
            "password" => $password,
            "service" => $service,
            "profile" => $profile,
        ));
    }


    public function updateExistingSecret($user, $password, $profile, $existingUsername){

        $this->comm("/ppp/secret/set", array(
            "numbers" => $existingUsername,
            "name" => $user,
            "password" => $password,
            "profile" => $profile,
        ));

    }
    public function updateOnlyprofile($user, $profile, $existingUsername){

        $this->comm("/ppp/secret/set", array(
            "numbers" => $existingUsername,
            "name" => $user,
            "profile" => $profile,
        ));

    }
    public function CreateFirewall($id, $ip, $block){
             $this->write('/ip/firewall/address-list/add',false);
            $this->write('=list='.$id,false);
            $this->write('=address='.$ip,false);
            $this->write('=comment='.$block,true);
            $READ = $this->read(false);
            $ARRAY = $this->parseResponse($READ);

    }
    public function RemoveFirewall($comment){
        $this->write('/ip/firewall/address-list/print',false);
        $this->write('?comment='.$comment,true);
        $READ = $this->read(false);
        $ARRAY = $this->parseResponse($READ);
        if(count($ARRAY)>0){
            $this->write('/ip/firewall/address-list/remove', false);
            $this->write('=.id=' . $ARRAY[0]['.id']);
            $READ = $this->read(false);
        }

    }
    public function RemoveSecret($user){
        $this->comm("/ppp/secret/remove", array(
            "numbers" => $user,
        ));
    }


    public function __destruct() {
        
        $this->disconnect();
    }

}
