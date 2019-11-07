<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 2019-02-24
 * Time: 19:36
 */

class ImmunizationRegistry {

	private static $wsdl = 'https://hl7v2-iz-r1.5-testing.nist.gov:8098/iztool/ws/iisService.wsdl';
	private static $username = 'vela1606';
	private static $password = '45b0f25ee01d1dd0d3e8420a7eab7280d8336be3ecd181c8e5c9d1576ec9eddf';
	private static $facility_id = 'vela1606';

	/**
	 * @var SoapClient
	 */
	private static $client;

	public function sendImmunization($params){
        include_once (ROOT. '/dataProvider/HL7Messages.php');
        $HL7Messages = new HL7Messages();
        $response = $HL7Messages->sendVXU($params, 'ImmunizationRegistry::SenderHandler');
        return $response;
	}

	public function getImmunizationHxByPid($pid){
	    include_once (ROOT. '/dataProvider/HL7Messages.php');
	    $HL7Messages = new HL7Messages();
        $response = $HL7Messages->sendQBP((object)['pid' => $pid], 'ImmunizationRegistry::SenderHandler');
        return $response;
	}


	public static function SenderHandler($hl7_msg){
	    try{
            self::setClient();
            $data = [];
            $data['username'] = self::$username;
            $data['password'] = self::$password;
            $data['facilityID'] = self::$facility_id;
            $data['hl7Message'] = $hl7_msg;
            $response = self::$client->submitSingleMessage($data);

            return array(
                'success' => true,
                'message' => $response->return
            );

        }catch (Exception $e){
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

    }

	public static function setClient(){
		self::$client = new SoapClient(self::$wsdl, [ 'soap_version' => SOAP_1_2 ]);
	}


}