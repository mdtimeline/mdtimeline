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
MSH|^~\&|^2.16.840.1.113883.3.72.5.20^ISO|^2.16.840.1.113883.3.72.5.21^ISO||^2.16.840.1.113883.3.72.5.23^ISO|20110531140551-0500||ORU^R01^ORU_R01|NIST-LRI-GU-002.00|T|2.5.1|||AL|NE|||||LRI_Common_Component^^2.16.840.1.113883.9.16^ISO~LRI_GU_Component^^2.16.840.1.113883.9.12^ISO~LRI_RU_Component^^2.16.840.1.113883.9.14^ISO
PID|1||PATID1234^^^&2.16.840.1.113883.3.72.5.30.2&ISO^MR||Jones^William^A||19610615|M||2106-3^White^HL70005
ORC|RE|ORD666555^^2.16.840.1.113883.3.72.5.24^ISO|R-991133^^2.16.840.1.113883.3.72.5.25^ISO|GORD874233^^2.16.840.1.113883.3.72.5.24^ISO||||||||57422^Radon^Nicholas^^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI
OBR|1|ORD666555^^2.16.840.1.113883.3.72.5.24^ISO|R-991133^^2.16.840.1.113883.3.72.5.25^ISO|57021-8^CBC W Auto Differential panel in Blood^LN^4456544^CBC^99USI^^^CBC W Auto Differential panel in Blood|||20110103143428-0800|||||||||57422^Radon^Nicholas^^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI||||||20110104170028-0800|||F|||10093^Deluca^Naddy^^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI|||||||||||||||||||||CC^Carbon Copy^HL70507
OBX|1|NM|26453-1^Erythrocytes [#/volume] in Blood^LN^^^^^^Erythrocytes [#/volume] in Blood||4.41|10*6/uL^million per microliter^UCUM|4.3 to 6.2|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|2|NM|718-7^Hemoglobin [Mass/volume] in Blood^LN^^^^^^Hemoglobin [Mass/volume] in Blood||12.5|g/mL^grams per milliliter^UCUM|13 to 18|L|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|3|NM|20570-8^Hematocrit [Volume Fraction] of Blood^LN^^^^^^Hematocrit [Volume Fraction] of Blood||41|%^percent^UCUM|40 to 52|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|4|NM|26464-8^Leukocytes [#/volume] in Blood^LN^^^^^^Leukocytes [#/volume] in Blood||105600|{cells}/uL^cells per microliter^UCUM|4300 to 10800|HH|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|5|NM|26515-7^Platelets [#/volume] in Blood^LN^^^^^^Platelets [#/volume] in Blood||210000|{cells}/uL^cells per microliter^UCUM|150000 to 350000|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|6|NM|30428-7^Erythrocyte mean corpuscular volume [Entitic volume]^LN^^^^^^Erythrocyte mean corpuscular volume [Entitic volume]||91|fL^femtoliter^UCUM|80 to 95|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|7|NM|28539-5^Erythrocyte mean corpuscular hemoglobin [Entitic mass]^LN^^^^^^Erythrocyte mean corpuscular hemoglobin [Entitic mass]||29|pg/{cell}^picograms per cell^UCUM|27 to 31|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|8|NM|28540-3^Erythrocyte mean corpuscular hemoglobin concentration [Mass/volume]^LN^^^^^^Erythrocyte mean corpuscular hemoglobin concentration [Mass/volume]||32.4|g/dL^grams per deciliter^UCUM|32 to 36|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|9|NM|30385-9^Erythrocyte distribution width [Ratio]^LN^^^^^^Erythrocyte distribution width [Ratio]||10.5|%^percent^UCUM|10.2 to 14.5|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|10|NM|26444-0^Basophils [#/volume] in Blood^LN^^^^^^Basophils [#/volume] in Blood||0.1|10*3/uL^thousand per microliter^UCUM|0 to 0.3|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|11|NM|30180-4^Basophils/100 leukocytes in Blood^LN^^^^^^Basophils/100 leukocytes in Blood||0.1|%^percent^UCUM|0 to 2|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|12|NM|26484-6^Monocytes [#/volume] in Blood^LN^^^^^^Monocytes [#/volume] in Blood||3|10*3/uL^thousand per microliter^UCUM|0.0 to 13.0|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|13|NM|26485-3^Monocytes/100 leukocytes in Blood^LN^^^^^^Monocytes/100 leukocytes in Blood||3|%^percent^UCUM|0 to 10|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|14|NM|26449-9^Eosinophils [#/volume] in Blood^LN^^^^^^Eosinophils [#/volume] in Blood||2.1|10*3/uL^thousand per microliter^UCUM|0.0 to 0.45|HH|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|15|NM|26450-7^Eosinophils/100 leukocytes in Blood^LN^^^^^^Eosinophils/100 leukocytes in Blood||2|%^percent^UCUM|0 to 6|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|16|NM|26474-7^Lymphocytes [#/volume] in Blood^LN^^^^^^Lymphocytes [#/volume] in Blood||41.2|10*3/uL^thousand per microliter^UCUM|1.0 to 4.8|HH|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|17|NM|26478-8^Lymphocytes/100 leukocytes in Blood^LN^^^^^^Lymphocytes/100 leukocytes in Blood||39|%^percent^UCUM|15.0 to 45.0|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|18|NM|26499-4^Neutrophils [#/volume] in Blood^LN^^^^^^Neutrophils [#/volume] in Blood||58|10*3/uL^thousand per microliter^UCUM|1.5 to 7.0|HH|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|19|NM|26511-6^Neutrophils/100 leukocytes in Blood^LN^^^^^^Neutrophils/100 leukocytes in Blood||55|%^percent^UCUM|50 to 73|N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|20|CWE|38892-6^Anisocytosis [Presence] in Blood^LN^^^^^^Anisocytosis [Presence] in Blood||260348001^Present ++ out of ++++^SCT^^^^^^Moderate Anisocytosis|||A|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|21|CWE|30400-6^Hypochromia [Presence] in Blood^LN^^^^^^Hypochromia [Presence] in Blood||260415000^not detected^SCT^^^^^^None seen|||N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|22|CWE|30424-6^Macrocytes [Presence] in Blood^LN^^^^^^Macrocytes [Presence] in Blood||260415000^not detected^SCT^^^^^^None seen|||N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|23|CWE|30434-5^Microcytes [Presence] in Blood^LN^^^^^^Microcytes [Presence] in Blood||260415000^not detected^SCT^^^^^^None seen|||N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|24|CWE|779-9^Poikilocytosis [Presence] in Blood by Light microscopy^LN^^^^^^Poikilocytosis [Presence] in Blood by Light microscopy||260415000^not detected^SCT^^^^^^None seen|||N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|25|CWE|10378-8^Polychromasia [Presence] in Blood by Light microscopy^LN^^^^^^Polychromasia [Presence] in Blood by Light microscopy||260415000^not detected^SCT^^^^^^None seen|||N|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|26|TX|6742-1^Erythrocyte morphology finding [Identifier] in Blood^LN^^^^^^Erythrocyte morphology finding [Identifier] in Blood||Many spherocytes present.|||A|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|27|TX|11156-7^Leukocyte morphology finding [Identifier] in Blood^LN^^^^^^Leukocyte morphology finding [Identifier] in Blood||Reactive morphology in lymphoid cells.|||A|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
OBX|28|TX|11125-2^Platelet morphology finding [Identifier] in Blood^LN^^^^^^Platelet morphology finding [Identifier] in Blood||Platelets show defective granulation.|||A|||F|||20110103143428-0800|||||20110103163428-0800||||Century Hospital^^^^^&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^987|2070 Test Park^^Los Angeles^CA^90067^^B|2343242^Knowsalot^Phil^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^DN
SPM|1|||119297000^BLD^SCT^^^^^^Blood|||||||||||||20110103143428-0800
MSG;


$msg = $HL7->readMessage($msg);
$print = $HL7->printMessage('HL7 MESSAGE - LABORATORY RESULTS REPORT');
print '<pre>';
print_r($print);
