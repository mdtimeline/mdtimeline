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


	public function getImmunizationHxByPid($pid){

	    include_once (ROOT. '/dataProvider/HL7Messages.php');
	    $HL7Messages = new HL7Messages();
        $response = $HL7Messages->sendQBP((object)['pid' => $pid], 'ImmunizationRegistry::SenderHandler');


        return $response;



	    $child_history = <<<HXX

MSH|^~\&|NISTEHRAPP|NISTEHRFAC|NISTIISAPP|NISTIISFAC|20141031145233-0500||QBP^Q11^QBP_Q11|NIST-IZ-QR-1.1_Query_Q11_Z44|P|2.5.1|||ER|AL|||||Z44^CDCPHINVS|NISTEHRFAC^^^^^NIST-AA-1^XX^^^100-6482|NISTIISFAC^^^^^NIST-AA-1^XX^^^100-3322
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-1.1-2015|171122^^^NIST-MPI-1^MR|Fairchild^Cameron^A^^^^L|Fairlady^^^^^^M|20090214|M|105 Laurel Run Rd^^Bozeman^MT^^^P
RCP|I|1^RD&Records&HL70126


MSH|^~\&|MDTIMELINE|Facility Carolina|TRA|Test Facility|20191016151932||QBP^Q11^QBP_Q11|MDTL-468451095071668|P|2.5.1|||ER|AL||||||NISTEHRFAC^NIST-AA-1^XX^100-6482|NISTIISFAC^NIST-AA-1^XX^100-3322
QBP|Z44^Request Evaluated History and Forecast^CDCPHINVS||171122^^^&&NPI^MR|Fairchild^Cameron^^^^^L|^^^^^^M|20090214000000|M|105 Laurel Run Rd ^^Bozeman^MT^^^L^^25025
RCP|I|1^ RRH


MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20151031145233-0500||RSP^K11^RSP_K11|NIST-IZ-QR-1.2_Response_K11_Z42|P|2.5.1|||NE|NE|||||Z42^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-1^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-1^XX^^^100-6482
MSA|AA|NIST-IZ-QR-1.1_Query_Q11_Z44
QAK|IZ-1.1-2015|OK|Z44^Request Evaluated History and Forecast^CDCPHINVS
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-1.1-2015|171122^^^NIST-MPI-1^MR|Fairchild^Cameron^A^^^^L|Fairlady^^^^^^M|20090214|M|105 Laurel Run Rd^^Bozeman^MT^^^P
PID|1||171122^^^NIST-MPI-1^MR~34500907^^^NIST-IIS-MPI^SR||Fairchild^Cameron^A^^^^L||20090214|M|||105 Laurel Run Rd^^Bozeman^MT^^^P


ORC|RE||197023^NIST-AA-IZ-2||||||||||||||NISTEHRFAC^NISTEHRFacility^HL70362
RXA|0|1|20090415||45^Hep B Unspec^CVX|999|||01^historical record^NIP001|||||||||||CP
OBX|1|CE|30956-7^vaccine type^LN|1|45^Hep B NOS^CVX||||||F|||20151031
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|ID|59781-5^dose validity^LN|1|Y||||||F|||20151031


ORC|RE||197027^NIST-AA-IZ-2||||||||||||||
RXA|0|1|20090314||48^HIB PRP-T^CVX|999|||01^historic immunization record^NIP001|5111^Sticker^Nurse^^^^^^NIST-PI-1^L^^^PRN|^^^NIST-Clinic-1|||||||||CP
OBX|1|CE|30956-7^vaccine type^LN|1|17^HIB NOS^CVX||||||F|||20151031
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|ID|59781-5^dose validity^LN|1|N||||||F|||20151031
OBX|4|ST|30982-3^Reason applied ^LN|1|Too Young||||||F|||20151031


ORC|RE||197028^NIST-AA-IZ-2||||||||||||||
RXA|0|1|20091011||110^DTAP-Hep B-IPV^CVX|999|||01^historic immunization record^NIP001|||||||||||CP

OBX|1|CE|30956-7^vaccine type^LN|1|45^Hep B NOS^CVX||||||F
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|ID|59781-5^dose validity^LN|1|Y||||||F|||20151031

OBX|4|CE|30956-7^vaccine type^LN|2|10^IPV^CVX||||||F
OBX|5|CE|59779-9^Immunization Schedule used^LN|2|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|6|ID|59781-5^dose validity^LN|2|Y||||||F|||20151031

OBX|7|CE|30956-7^vaccine type^LN|3|20^DTAP^CVX||||||F
OBX|8|CE|59779-9^Immunization Schedule used^LN|3|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|9|ID|59781-5^dose validity^LN|3|Y||||||F|||20151031


ORC|RE||197028^NIST-AA-IZ-2||||||||||||||
RXA|0|1|20100411||08^Hep BPEDS^CVX|999|||01^historic immunization record^NIP001|5111^Sticker^Nurse^^^^^^NIST-PI-1^L^^^PRN||||||||||CP
OBX|1|CE|30956-7^vaccine type^LN|1|45^Hep B NOS^CVX||||||F|||20151031
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|ID|59781-5^dose validity^LN|1|Y||||||F|||20151031



ORC|RE||197028^NIST-AA-IZ-2||||||||||||||
RXA|0|1|20100415||03^MMR^CVX|999|||01^historic immunization record^NIP001|||||||||||CP
OBX|1|CE|30956-7^vaccine type^LN|1|03^MMR^CVX||||||F|||20151031
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|ID|59781-5^dose validity^LN|1|Y||||||F|||20151031



ORC|RE||9999^NIST-AA-IZ-2||||||||||||||NISTEHRFAC^NISTEHRFacility^HL70362
RXA|0|1|20091031||998^no vaccine admin^CVX|999||||||||||||||NA
OBX|1|CE|30956-7^vaccine type^LN|1|03^MMR^CVX||||||F|||20151031
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|3|DT|30980-7^Date vaccination due^LN|1|20150214||||||F|||20151031
OBX|4|DT|30981-5^Earliest Date to give^LN|1|20100614||||||F|||20151031
OBX|5|CE|30956-7^vaccine type^LN|2|10^IPV^CVX||||||F|||20151031
OBX|6|CE|59779-9^Immunization Schedule used^LN|2|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|7|DT|30980-7^Date vaccination due^LN|2|20100214||||||F|||20151031
OBX|8|CE|30956-7^vaccine type^LN|3|107^DTAP^CVX||||||F|||20151031
OBX|9|CE|59779-9^Immunization Schedule used^LN|3|VXC16^ACIP^CDCPHINVS||||||F|||20151031
OBX|10|DT|30980-7^Date vaccination due^LN|3|20100214||||||F|||20151031
HXX;

        $adult_history = <<<AHX
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20141031145233-0500||RSP^K11^RSP_K11|NIST-IZ-QR-2.2_Response_K11_Z42|P|2.5.1|||NE|NE|||||Z42^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-1^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-1^XX^^^100-6482
MSA|AA|NIST-IZ-QR-2.1_Query_Q11_Z44
QAK|IZ-2.1-2015|OK|Z44^Request Evaluated History and Forecast^CDCPHINVS
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-2.1-2015|648286^^^NIST-MPI-1^MR|Stanley^Clement^S^^^^L|Bell^^^^^^M|19500214|M|1642 Bear Run^^Bozeman^MT^59715^USA^P|^PRN^PH^^1^406^5552020
PID|1||648286^^^NIST-MPI-1^MR^MR~34500907^^^NIST-IIS-MPI^SR||Stanley^Clement^S^^^^L||19500214|M|||1642 Bear Run^^Bozeman^MT^59715^USA^P
ORC|RE||193337027^NIST-AA-IZ-2||||||||||||||
RXA|0|1|20151029||140^seasonal flu^CVX|0.5|mL^^UCUM||00^new immunization record^NIP001|5111^Sticker^Nurse^^^^^^NIST-PI-1^L^^^PRN|^^^NIST-Clinic-1||||||CSL^bioCSL^MVX|||CP
RXR|C28161^IM^NCIT
OBX|1|CE|30956-7^vaccine type^LN|1|88^influenza NOS^CVX||||||F
OBX|2|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F|||20151029
OBX|3|ID|59781-5^dose validity^LN|1|Y||||||F|||20151029
ORC|RE||9999^NIST-AA-IZ-2||||||||||||||NISTEHRFAC^NISTEHRFacility^HL70362
RXA|0|1|20151029||998^no vaccine admin^CVX|999||||||||||||||NA
OBX|4|CE|30956-7^vaccine type^LN|1|152^pcv NOS^CVX||||||F
OBX|5|CE|59779-9^Immunization Schedule used^LN|1|VXC16^ACIP^CDCPHINVS||||||F
OBX|6|DT|30980-7^Date vaccination due^LN|1|20600101||||||F
OBX|7|DT|30981-5^Earliest Date to give^LN|1|20600101||||||F
OBX|8|CE|30956-7^vaccine type^LN|2|88^influenza NOS^CVX||||||F
OBX|9|CE|59779-9^Immunization Schedule used^LN|2|VXC16^ACIP^CDCPHINVS||||||F
OBX|10|DT|30980-7^Date vaccination due^LN|2|20160901||||||F
OBX|11|DT|30981-5^Earliest Date to give^LN|2|20160901||||||F
OBX|12|CE|30956-7^vaccine type^LN|3|03^MMR^CVX||||||F
OBX|13|CE|59779-9^Immunization Schedule used^LN|3|VXC16^ACIP^CDCPHINVS||||||F
OBX|14|DT|30980-7^Date vaccination due^LN|3|19960101||||||F
OBX|15|DT|30981-5^Earliest Date to give^LN|3|19960101||||||F
OBX|16|CE|30956-7^vaccine type^LN|4|85^Hep A^CVX||||||F
OBX|17|CE|59779-9^Immunization Schedule used^LN|4|VXC16^ACIP^CDCPHINVS||||||F
OBX|18|DT|30980-7^Date vaccination due^LN|4|19960101||||||F
OBX|19|DT|30981-5^Earliest Date to give^LN|4|19960101||||||F
OBX|20|CE|30956-7^vaccine type^LN|5|45^Hep B^CVX||||||F
OBX|21|CE|59779-9^Immunization Schedule used^LN|5|VXC16^ACIP^CDCPHINVS||||||F
OBX|22|DT|30980-7^Date vaccination due^LN|5|19950101||||||F
OBX|23|DT|30981-5^Earliest Date to give^LN|5|19950101||||||F
OBX|24|CE|30956-7^vaccine type^LN|6|108^MCV^CVX||||||F
OBX|25|CE|59779-9^Immunization Schedule used^LN|6|VXC16^ACIP^CDCPHINVS||||||F
OBX|26|DT|30980-7^Date vaccination due^LN|6|20110101||||||F
OBX|27|DT|30981-5^Earliest Date to give^LN|6|20110101||||||F
OBX|28|CE|30956-7^vaccine type^LN|7|21^varicella^CVX||||||F
OBX|29|CE|59779-9^Immunization Schedule used^LN|7|VXC16^ACIP^CDCPHINVS||||||F
OBX|30|DT|30980-7^Date vaccination due^LN|7|19960101||||||F
OBX|31|DT|30981-5^Earliest Date to give^LN|7|19960101||||||F
OBX|32|CE|30956-7^vaccine type^LN|8|115^TDAP^CVX||||||F
OBX|33|CE|59779-9^Immunization Schedule used^LN|8|VXC16^ACIP^CDCPHINVS||||||F
OBX|34|DT|30980-7^Date vaccination due^LN|8|20020101||||||F
OBX|35|DT|30981-5^Earliest Date to give^LN|8|20020101||||||F
AHX;

        $no_patient = <<<NPHX
MSH|^~\&|NISTIISAPP^2.16.840.1.113883.3.72.5.40.3^ISO|NISTIISFAC^2.16.840.1.113883.3.72.5.40.4^ISO|NISTEHRAPP^2.16.840.1.113883.3.72.5.40.1^ISO|NISTEHRFAC^2.16.840.1.113883.3.72.5.40.2^ISO|20151031145233-0500||RSP^K11^RSP_K11|NIST-IZ-QR-3.2_Response_NF_K11_Z33|P|2.5.1|||NE|NE|||||Z33^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-IZ-1&2.16.840.1.113883.3.72.5.40.9&ISO^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-IZ-1&2.16.840.1.113883.3.72.5.40.9&ISO^XX^^^100-6482
MSA|AA|NIST-IZ-QR-3.1_Query_Q11_Z44
QAK|IZ-3.1-2015|NF|Z44^Request Evaluated History and Forecast^CDCPHINVS
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-3.1-2015|177993^^^NIST-MPI-1&2.16.840.1.113883.3.72.5.40.5&ISO^MR|Bee^Donna^Victoria^^^^L|Goluchio^^^^^^M|20120507|F|737 Everygreen Road^Suite 511^Livingston^MT^59047^USA^P|^PRN^PH^^^406^5553758|Y|3

NPHX;

        $too_many = <<<TOMANY
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20141031145233-0500||RSP^K11^RSP_K11|NIST-IZ-QR-4.2_Response_TM_K11_Z33|P|2.5.1|||NE|NE|||||Z33^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-1^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-1^XX^^^100-6482
MSA|AA|NIST-IZ-QR-4.1_Query_Q11_Z44
QAK|IZ-4.1-2015|TM|Z44^Request Evaluated History and Forecast^CDCPHINVS
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-4.1-2015|412299^^^NIST-MPI-1^MR|Smith^James^^^^^L||20110905|M|22 East Branch Way^^Livingston^MT^59047^^P|^PRN^PH^^^406^5556285

TOMANY;

        $ack_ok = <<<ACKOK
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20150624073734.034-0500||ACK^V04^ACK|NIST-IZ-AD-1.2_Receive_ACK_Z23|P|2.5.1|||NE|NE|||||Z23^CDCPHINVS|NISTIISFAC|NISTEHRFAC
MSA|AA|NIST-IZ-AD-1.1_Send_V04_Z22
ACKOK;
        $ack_error1 = <<<ACKOK
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20150625121047.853-0500||ACK^V04^ACK|NIST-IZ-AD-7.2_Receive_ACK_Z23|P|2.5.1|||NE|NE|||||Z23^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-IZ-1^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-IZ-1^XX^^^100-6482
MSA|AE|NIST-IZ-AD-7.1_Send_V04_Z22
ERR||RXA^1^5^1^1|999^Application error^HL70357|E|5^Table value not found^HL70533|||Vaccine code not recognized - message rejected
ACKOK;
        $ack_error2 = <<<ACKOK
MSH|^~\&|NISTIISAPP^2.16.840.1.113883.3.72.5.40.3^ISO|NISTIISFAC^2.16.840.1.113883.3.72.5.40.4^ISO|NISTEHRAPP^2.16.840.1.113883.3.72.5.40.1^ISO|NISTEHRFAC^2.16.840.1.113883.3.72.5.40.2^ISO|20150625103328.758-0500||ACK^V04^ACK|NIST-IZ-AD-8.2_Receive_ACK_Z23|P|2.5.1|||NE|NE|||||Z23^CDCPHINVS|NISTIISFAC^^^^^NIST-AA-IZ-1&2.16.840.1.113883.3.72.5.40.9&ISO^XX^^^100-3322|NISTEHRFAC^^^^^NIST-AA-IZ-1&2.16.840.1.113883.3.72.5.40.9&ISO^XX^^^100-6482
MSA|AE|NIST-IZ-AD-8.1_Send_V04_Z22
ERR||RXR^1^2^1^1|999^Application error^HL70357|W|5^Table value not found^HL70533|||Administration Site not recognized - site data will not be saved
ACKOK;
        $ack_error3 = <<<ACKOK
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20150625115038.044-0500||ACK^V04^ACK|NIST-IZ-AD-9.2_Receive_ACK_Z23|P|2.5.1|||NE|NE|||||Z23^CDCPHINVS|NISTIISFAC|NISTEHRFAC
MSA|AE|NIST-IZ-AD-9.1_Send_V04_Z22
ERR||RXR^1^2^1^1|999^Application error^HL70357|W|5^Table value not found^HL70533|||Administration Site not recognized - site data will not be saved
ERR||RXR^2^2^1^1|999^Application error^HL70357|W|5^Table value not found^HL70533|||Administration Site not recognized - site data will not be saved
ACKOK;
        $ack_error4 = <<<ACKOK
MSH|^~\&|NISTIISAPP|NISTIISFAC|NISTEHRAPP|NISTEHRFAC|20150625072816.602-0500||ACK^V04^ACK|NIST-IZ-AD-10.2_Receive_ACK_Z23|P|2.5.1|||NE|NE|||||Z23^CDCPHINVS|NISTIISFAC|NISTEHRFAC
MSA|AR|NIST-IZ-AD-10.1_Send_V04_Z22
ERR||MSH^1^12^1^1|203^Unsupported Version ID^HL70357|E||||Version ID not recognized - message rejected
ACKOK;


		$hl7 = <<<HL7
MSH|^~\&|NISTEHRAPP|NISTEHRFAC|NISTIISAPP|NISTIISFAC|20150531145156-0500||QBP^Q11^QBP_Q11|NIST-IZ-QR-4.1_Query_Q11_Z44|P|2.5.1|||ER|AL|||||Z44^CDCPHINVS|NISTEHRFAC^^^^^NIST-AA-1^XX^^^100-6482|NISTIISFAC^^^^^NIST-AA-1^XX^^^100-3322
QPD|Z44^Request Evaluated History and Forecast^CDCPHINVS|IZ-4.1-2015|412299^^^NIST-MPI-1^MR|Smith^James^^^^^L||20110905|M|22 East Branch Way^^Livingston^MT^59047^^P|^PRN^PH^^^406^5556285
RCP|I|1^RD&Records&HL70126

HL7;

		include_once ('../lib/HL7/HL7.php');
		$HL7 = new HL7();
		$msg = $HL7->readMessage($ack_error1);

		print '<pre>';
		print_r($HL7->printMessage('IMMUNIZATION HX RESPONSE'));

//		$this->setClient();
//
//		$data = [];
//		$data['username'] = $this->username;
//		$data['password'] = $this->password;
//		$data['facilityID'] = $this->facility_id;
//		$data['hl7Message'] = $hl7;
//
////		$data['echoBack'] = 'Hello world!';
//
//		$response = $this->client->submitSingleMessage($data);
//
//		print '<pre>';
//		print_r($response);
//
//
//		return [];

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


//$ImmunizationRegistry = new ImmunizationRegistry();
//$ImmunizationRegistry->getImmunizationHx(true);