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
MSH|^~\&|^2.16.840.1.113883.3.72.5.20^ISO|^2.16.840.1.113883.3.72.5.21^ISO||^2.16.840.1.113883.3.72.5.23^ISO|20110531140551-0500||ORU^R01^ORU_R01|NIST-LRI-GU-RU-005.00|T|2.5.1|||AL|NE|||||LRI_Common_Component^^2.16.840.1.113883.9.16^ISO~LRI_GU_Component^^2.16.840.1.113883.9.12^ISO~LRI_RU_Component^^2.16.840.1.113883.9.14^ISO
PID|1||M0313831^^^&2.16.840.1.113883.3.72.5.30.2&ISO^MR||Smirnoff^Peggy^^^^^M||19750401|F||2106-3^White^HL70005^wh^white^L
ORC|RE|1^^2.16.840.1.113883.3.72.5.24^ISO|R-511^^2.16.840.1.113883.3.72.5.25^ISO|||||||||1234567890^Fine^Larry^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI
OBR|1|1^^2.16.840.1.113883.3.72.5.24^ISO|R-511^^2.16.840.1.113883.3.72.5.25^ISO|HepABC Panel^Hepatitis A B C Panel^L|||20120628070100|||||||||1234567890^Fine^Larry^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI||||||20120629132900-0500|||F
OBX|1|CWE|22314-9^Hepatitis A virus IgM Ab [Presence] in Serum^LN^HAVM^Hepatitis A IgM antibodies (IgM anti-HAV)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|2|CWE|20575-7^Hepatitis A virus Ab [Presence] in Serum^LN^HAVAB^Hepatitis A antibodies (anti-HAV)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|3|CWE|16933-4^Hepatitis B virus core Ab [Presence] in Serum^LN^HBVcAB^Hepatitis B core antibodies (anti-HBVc)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|4|SN|22316-4^Hepatitis B virus core Ab [Units/volume] in Serum^LN^HBcAbQ^Hepatitis B core antibodies (anti-HBVc) Quant^L||^0.60|[IU]/mL^international unit per milliliter^UCUM^IU/ml^^L|<0.50 IU/mL|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|5|CWE|22320-6^Hepatitis B virus e Ab [Presence] in Serum^LN^HBVeAB^Hepatitis B e antibodies (anti-HBVe)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|6|CWE|5195-3^Hepatitis B virus surface Ag [Presence] in Serum^LN^HBVsAG^Hepatitis B surface antigen (HBsAg)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|7|CWE|22322-2^Hepatitis B virus surface Ab [Presence] in Serum^LN^HBVSAB^Hepatitis B surface antibody (anti-HBVs)^L||260385009^Negative (qualifier value)^SCT^NEG^NEGATIVE^L^^^Negative (qualifier value)||Negative|N|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|8|CWE|16128-1^Hepatitis C virus Ab [Presence] in Serum^LN^HCVAB^Hepatitis C antibody screen  (anti-HCV)^L||10828004^Positive (qualifier value)^SCT^POS^POSITIVE^L^^^Positive (qualifier value)||Negative|A|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
OBX|9|SN|48159-8^Hepatitis C virus Ab Signal/Cutoff in Serum or Plasma by Immunoassay^LN^HCVSCO^Hepatitis C antibodies Signal to Cut-off Ratio^L||^10.8|{s_co_ratio}^Signal to cutoff ratio^UCUM^s/co^^L|0.0-0.9 s/co|H|||F|||20120628070100|||||20120628100500||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
NTE|1||Negative:   < 0.8; Indeterminate 0.8 - 0.9; Positive:  > 0.9.  In order to reduce the incidence of a false positive result, the CDC recommends that all s/co ratios between 1.0 and 10.9 be confirmed with additional Verification or PCR testing.
SPM|1|||119364003^Serum specimen (specimen)^SCT^SER^Serum^L|||||||||||||20120628070100
ORC|RE||R-512^^2.16.840.1.113883.3.72.5.25^ISO|||||||||1234567890^Fine^Larry^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI
OBR|2||R-512^^2.16.840.1.113883.3.72.5.25^ISO|11011-4^Hepatitis C virus RNA [Units/volume] (viral load) in Serum or Plasma by Probe and target amplification method^LN^HCVRNA^Hepatitis C RNA PCR^L|||20120628070100||||G|||||1234567890^Fine^Larry^^^Dr.^^^&2.16.840.1.113883.3.72.5.30.1&ISO^L^^^NPI||||||20120629132900-0500|||F|16128-1&Hepatitis C virus Ab [Presence] in Serum&LN&HCVAB&Hepatitis C antibody screen  (anti-HCV)&L|||723248&&2.16.840.1.113883.3.72.5.24&ISO^R-511&&2.16.840.1.113883.3.72.5.25&ISO
OBX|1|SN|11011-4^Hepatitis C virus RNA [Units/volume] (viral load) in Serum or Plasma by Probe and target amplification method^LN^HCVRNA^Hepatitis C RNA PCR^L||^7611200|[IU]/mL^international unit per milliliter^UCUM^IU/ml^^L|<43 IU/mL|H|||F|||20120628070100|||||20120629092700||||Princeton Hospital Laboratory^^^^^NIST HCAA-1&2.16.840.1.113883.3.72.5.30.1&ISO^XX^^^34D4567890|123 High Street^^Princeton^NJ^08540^USA^O^^34021|^Martin^Steven^M^^Dr.
MSG;


$msg = $HL7->readMessage($msg);
$print = $HL7->printMessage('HL7 MESSAGE - LABORATORY RESULTS REPORT');
print '<pre>';
print_r($print);
