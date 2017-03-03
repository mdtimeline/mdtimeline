<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 8/29/13
 * Time: 12:13 PM
 * To change this template use File | Settings | File Templates.
 */

//$WshShell = new COM("WScript.Shell");
//$oExec = $WshShell->Run('php -f ".\HL7Server.php" -- "C:/path/" "site" "class" "function"', 0, false);
//set_time_limit(0);
//$cmd = '.\start.bat "127.0.0.1" 9100 "C:/inetpub/wwwroot/gaiaehr/dataProvider" "HL7Server" "Process" "default"';
//if (substr(php_uname(), 0, 7) == "Windows"){
////	system($cmd);
//	pclose(popen($cmd, "r"));
//	exit;
//}
//else {
//	exec($cmd . " > /dev/null &");
//	exit;
//}
include_once ('HL7.php');
$HL7 = new HL7();
$msg = <<<MSG
MSH|^~\&|NIST Test Lab APP^2.16.840.1.113883.3.72.5.20^ISO|NIST Lab Facility^2.16.840.1.113883.3.72.5.21^ISO||NIST EHR Facility^2.16.840.1.113883.3.72.5.23^ISO|20110531140551-0500||ORU^R01^ORU_R01|NIST-LRI-GU-001.00|T|2.5.1|||AL|NE|||||LRI_Common_Component^Profile Component^2.16.840.1.113883.9.16^ISO~LRI_GU_Component^Profile Component^2.16.840.1.113883.9.12^ISO~LRI_RU_Component^Profile Component^2.16.840.1.113883.9.14^ISO
PID|1||PATID1234^^^NIST MPI&2.16.840.1.113883.3.72.5.30.2&ISO^MR||Jones^William^A^JR^^^L||19610615|M||2106-3^White^HL70005^CAUC^Caucasian^L
ORC|RE|ORD723222^NIST EHR^2.16.840.1.113883.3.72.5.24^ISO|R-783274^NIST Lab Filler^2.16.840.1.113883.3.72.5.25^ISO|GORD874211^NIST EHR^2.16.840.1.113883.3.72.5.24^ISO||||||||57422^Radon^Nicholas^M^JR^DR^^^NIST-AA-1&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI
OBR|1|ORD723222^NIST EHR^2.16.840.1.113883.3.72.5.24^ISO|R-783274^NIST Lab Filler^2.16.840.1.113883.3.72.5.25^ISO|30341-2^Erythrocyte sedimentation rate^LN^815115^Erythrocyte sedimentation rate^99USI^^^Erythrocyte sedimentation rate|||20110331140551-0800||||L||7520000^fever of unknown origin^SCT^22546000^fever, origin unknown^99USI^^^Fever of unknown origin|||57422^Radon^Nicholas^M^JR^DR^^^NIST-AA-1&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI||||||20110331160428-0800|||F|||10092^Hamlin^Pafford^M^Sr.^Dr.^^^NIST-AA-1&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI|||||||||||||||||||||CC^Carbon Copy^HL70507^C^Send Copy^L^^^Copied Requested
NTE|1||Patient is extremely anxious about needles used for drawing blood.
TQ1|1||||||20110331150028-0800|20110331152028-0800
OBX|1|NM|30341-2^Erythrocyte sedimentation rate^LN^815117^ESR^99USI^^^Erythrocyte sedimentation rate||10|mm/h^millimeter per hour^UCUM|0 to 17|N|||F|||20110331140551-0800|||||20110331150551-0800||||Century Hospital^^^^^NIST-AA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^USA^B^^06037|2343242^Knowsalot^Phil^J.^III^Dr.^^^NIST-AA-1&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
SPM|1|||119297000^BLD^SCT^BldSpc^Blood^99USA^^^Blood Specimen|||||||||||||20110331140551-0800|||||||COOL^Cool^HL70493^CL^Cool^99USA^^^Cool
MSG;


$msg = $HL7->readMessage($msg);
$print = $HL7->printMessage('HL7 MESSAGE - LABORATORY RESULTS REPORT');
print '<pre>';
print_r($print);
