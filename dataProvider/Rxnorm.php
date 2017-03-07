<?php

/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Rxnorm
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * @var MatchaCUP
     */
    private $i;

    function __construct()
    {
        $this->db = Matcha::getConn();
    }

    function setInstructionModel()
    {
        if (isset($this->i)) return $this->i;
        return $this->i = MatchaModel::setSenchaModel('App.model.administration.MedicationInstruction');
    }

    public function getStrengthByCODE($CODE)
    {
        $sth = $this->db->prepare("SELECT ATV
		                     FROM rxnsat
		                    WHERE `CODE` = :c
		                      AND ATN    = 'DST'
		                      AND SAB    = 'RXNORM'");
        $sth->execute([':c' => $CODE]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['ATV'];
    }

    public function getDrugRouteByCODE($CODE)
    {
        $sth = $this->db->prepare("SELECT ATV
		                     FROM rxnsat
		                    WHERE `CODE` = :c
		                      AND ATN    = 'DRT'
		                      AND SAB    = 'RXNORM'");
        $sth->execute([':c' => $CODE]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['ATV'];
    }

    public function getDoseformByCODE($CODE)
    {
        $sth = $this->db->prepare("SELECT ATV
		                     FROM rxnsat
		                    WHERE `CODE` = :c
		                      AND ATN    = 'NDC'
		                      AND SAB    = 'RXNORM'");
        $sth->execute([':c' => $CODE]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['ATV'];
    }

	public function getNDCByRxCUI($RXCUI)
	{
		$sth = $this->db->prepare("SELECT ATV
		                     FROM rxnsat
		                    WHERE `RXCUI` = :c
		                      AND ATN    = 'NDC'");
		$sth->execute([':c' => $RXCUI]);
		$rec = $sth->fetch(PDO::FETCH_ASSOC);
		return $rec['ATV'];
	}

    /**
     * getIngredient
     * Method to do search in the RxNorm medication by it's ingredients, this depends if the search
     * is made by SCD, SCDG (Clinic) or SBDC, SBD (Brand)
     * @param $RxNormCode
     * @return array
     */
    public function getIngredient($RxNormCode)
    {
        // Fetch the RxNorm entire record, this because we need the entire RxNorm Concepts record
        $Statement = $this->db->prepare("
            SELECT * 
            FROM rxnconso 
            WHERE CODE=:code 
            AND (
                TTY='BN' OR 
                TTY='SCDG' OR 
                TTY='SBDC' OR
                TTY='IN' OR
                TTY='PIN' OR
                TTY='MIN' OR
                TTY='SBD' OR
                TTY='SBDC' OR
                TTY='SBDF'
            )");
        $Statement->execute([':code' => $RxNormCode]);
        $RxNormRecord = $Statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($RxNormRecord) > 0) {
            $RxNormRecord = $RxNormRecord[0];
        } else {
            return [];
        }
        switch ($RxNormRecord['TTY']) {
            case 'BN':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    INNER JOIN (
                        SELECT RXCUI1 FROM rxnrel 
                        WHERE RXCUI2 = :rxcui AND RELA='tradename_of'
                    ) AS rxnrel ON rxnrel.RXCUI1 = rxnconso.RXCUI
                    WHERE rxnconso.SAB = 'RXNORM' AND TTY='IN'");
                break;
            case 'IN':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    WHERE RXCUI=:rxcui AND TTY='IN' AND SAB='RXNORM'");
                break;
            case 'PIN':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    INNER JOIN (
                        SELECT RXCUI1 FROM rxnrel 
                        WHERE RXCUI2=:rxcui AND RELA='form_of'
                    ) AS rxnrel ON rxnrel.RXCUI1 = rxnconso.RXCUI
                    WHERE rxnconso.SAB='RXNORM' AND TTY='IN'");
                break;
            case 'MIN':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    INNER JOIN (
                        SELECT RXCUI1 FROM rxnrel 
                        WHERE RXCUI2=:rxcui AND RELA='has_part'
                    ) AS rxnrel ON rxnrel.RXCUI1 = rxnconso.RXCUI
                    WHERE rxnconso.SAB='RXNORM' AND TTY='IN'");
                break;
            case 'SBD':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    WHERE rxnconso.RXCUI IN (
                        # Step 2
                        SELECT RXCUI1 as RXCUI FROM rxnrel 
                        WHERE RXCUI2 IN (
                            # Step 1
                            SELECT RXCUI1 as RXCUI2 FROM rxnrel 
                            WHERE RXCUI2 = :rxcui AND RELA='consists_of'
                        ) AND RELA = 'has_ingredient'
                    ) AND SAB = 'RXNORM' AND TTY='IN'");
                break;
            case 'SBDC':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    WHERE rxnconso.RXCUI IN (
                        # Step 2
                        SELECT RXCUI1 as RXCUI FROM rxnrel 
                        WHERE RXCUI2 IN (
                            # Step 1
                            SELECT RXCUI1 as RXCUI2 FROM rxnrel 
                            WHERE RXCUI2 = :rxcui AND RELA='tradename_of'
                        ) AND RELA = 'has_ingredient'
                    ) AND SAB = 'RXNORM' AND TTY='IN'");
                break;
            case 'SCDG':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    INNER JOIN (
                        SELECT RXCUI1 FROM rxnrel 
                        WHERE RXCUI2=:rxcui AND RELA='has_ingredient'
                    ) AS rxnrel ON rxnrel.RXCUI1 = rxnconso.RXCUI
                    WHERE rxnconso.SAB='RXNORM' AND TTY='IN'");
                break;
            case 'SBDC':
            case 'SBDF':
                $Statement = $this->db->prepare("
                    SELECT * FROM rxnconso 
                    WHERE rxnconso.RXCUI IN (
                        # Step 2
                        SELECT RXCUI1 as RXCUI FROM rxnrel WHERE RXCUI2 IN (
                            # Step 1
                            SELECT RXCUI1 as RXCUI2 FROM rxnrel WHERE RXCUI2 = :rxcui AND RELA='tradename_of'
                        ) AND RELA = 'has_ingredient'
                    ) AND SAB = 'RXNORM' AND TTY='IN'");
                break;
        }
        $Statement->execute([':rxcui' => $RxNormRecord['RXCUI']]);
        return $Statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDoseformAbbreviateByCODE($CODE)
    {
        $sth = $this->db->prepare("SELECT ATV
		                     FROM rxnsat
		                    WHERE `CODE` = :c
		                      AND ATN    = 'DDFA'
		                      AND SAB    = 'RXNORM'");
        $sth->execute([':c' => $CODE]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['ATV'];
    }

    public function getDatabaseShortNameByCODE($CODE)
    {
        $sth = $this->db->prepare("SELECT SAB
		                     FROM rxnsat
		                    WHERE `CODE` = :c
                              AND SAB    = 'RXNORM'");
        $sth->execute([':c' => $CODE]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['SAB'];
    }

    public function getMedicationNameByRXCUI($RXCUI)
    {
        $sth = $this->db->prepare("SELECT STR
		                     FROM rxnconso
		                    WHERE RXCUI = :c
		                 GROUP BY RXCUI");
        $sth->execute([':c' => $RXCUI]);
        $rec = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $rec['STR'];
    }

    public function getRXNORMLiveSearch(stdClass $params)
    {
        $include_ingredients = isset($params->include_ingredients) ? $params->include_ingredients : false;
        $ingredients = $include_ingredients ? 'OR c.TTY=\'IN\'' : '';

        $include_groups = isset($params->include_groups) ? $params->include_groups : false;
        $groups = $include_groups ? 'OR c.`TTY` = \'SBDC\' OR c.`TTY`=\'SCDG\'' : '';

        $include_umls = isset($params->include_umls) ? $params->include_umls : false;
        $umls = $include_umls ? '(SELECT rxnsat.`ATV` FROM rxnsat WHERE `rxnsat`.`RXCUI` = RX.RXCUI AND (`rxnsat`.`ATN` = \'UMLSAUI\' OR `rxnsat`.`ATN` = \'UMLSCUI\') AND `rxnsat`.`ATV` IS NOT NULL LIMIT 1) AS UMLSCUI' : '';

        if (is_numeric($params->query)) {
            $where = 'c.`RXCUI` = :q';
            $query = [':q' => $params->query];
        } else {
            $where = 'c.`STR` LIKE :q';
            $query = [':q' => '%' . $params->query . '%'];
        }

        $sth = $this->db->prepare("
			SELECT RX.*,
		    (SELECT rxnsat.`ATV` FROM rxnsat WHERE `rxnsat`.`RXCUI` = RX.RXCUI AND `rxnsat`.`ATN` = 'NDC' AND `rxnsat`.`ATV` IS NOT NULL LIMIT 1) AS NDC,
			{$umls}
		  	(SELECT rxnconso.`code` FROM rxnconso WHERE `rxnconso`.`RXCUI` = RX.RXCUI AND (`rxnconso`.`SAB` = 'GS') LIMIT 1) AS GS_CODE
		    FROM (
		    	SELECT * 
			    FROM rxnconso as c 
			    WHERE ($where) 
			    AND c.`SAB` = 'RXNORM' 
			    AND (c.`TTY` = 'SCD' OR c.`TTY` = 'SBD' {$groups} {$ingredients}) 
			    LIMIT 50
		    ) AS RX
		    WHERE RX.SUPPRESS = 'N' AND RX.CVF <> ''
		    GROUP BY RX.`STR` 
		    ORDER BY RX.`TTY` DESC, RX.`STR` DESC;");

//        $sth = $this->db->prepare("SELECT RX.*, `rxnsat`.`ATV` AS NDC,
//                    (select code from rxnconso where SAB='GS' AND rxcui = RX.rxcui LIMIT 1) as GS_CODE
//                     FROM `rxnconso` AS RX
//               INNER JOIN `rxnsat`
//                       ON RX.`RXCUI` = `rxnsat`.`RXCUI`
//                          AND RX.`SAB` = 'RXNORM'
//                          AND (`rxnsat`.`ATN` = 'NDC' $umls)
//                          AND (RX.`TTY` = 'SCD' OR RX.`TTY` = 'SBD' {$groups} {$ingredients})
//                    WHERE ($where)
//                    AND (select code from `rxnconso` where SAB='GS' AND rxcui = RX.rxcui LIMIT 1) IS NOT NULL
//                 GROUP BY RX.`STR`
//                 ORDER BY RX.`TTY` DESC
//                    LIMIT 100");

        $sth->execute($query);
        $records = $sth->fetchAll(PDO::FETCH_ASSOC);
        $records = array_values($records);
        $total = count($records);
        $records = array_slice($records, $params->start, $params->limit);
        return [
            'totals' => $total,
            'rows' => $records
        ];
    }

    public function getRXNORMList(stdClass $params)
    {
        if (isset($params->query)) {
            $sth = $this->db->prepare("SELECT * FROM rxnconso
                                        WHERE (SAB = 'RXNORM' AND TTY = 'BD')
                                        AND STR LIKE :q
                                        GROUP BY RXCUI LIMIT 500");
            $sth->execute([':q' => $params->query . '%']);
        } else {
            $sth = $this->db->prepare("SELECT * FROM rxnconso
                                        WHERE (SAB = 'RXNORM'
                                        AND TTY = 'BD')
                                        GROUP BY RXCUI LIMIT 500");
            $sth->execute();
        }
        $records = $sth->fetchAll(PDO::FETCH_ASSOC);
        $total = count($records);
        $records = array_slice($records, $params->start, $params->limit);
        return [
            'totals' => $total,
            'data' => $records
        ];
    }

    /**
     * getRXNORMAllergyLiveSearch
     * Method called from the GaiaEHR Web Client, to look for a Medication
     * that cause allergy to the patient. The search can be either by RxCUI Code or by Medication Name.
     * This includes Ingridient Name, Brand Name
     * @param stdClass $params
     * @return array
     */
    public function getRXNORMAllergyLiveSearch(stdClass $params)
    {
        try {
            $sth = $this->db->prepare("SELECT *
                 FROM rxnconso
                WHERE SAB='RXNORM'
                AND (TTY = 'IN' OR TTY = 'PIN' OR TTY = 'BN' OR TTY='MIN' OR TTY='SBD' OR TTY='SBDC' OR TTY='SBDF' OR TTY='SCDG')
                AND (RXCUI LIKE :q1 OR STR LIKE :q2)
             GROUP BY RXCUI LIMIT 100");
            $sth->execute([
                ':q1' => $params->query . '%',
                ':q2' => '%' . $params->query . '%'
            ]);
            $records = $sth->fetchAll(PDO::FETCH_ASSOC);
            $total = count($records);
            $records = array_slice($records, $params->start, $params->limit);
            return [
                'totals' => $total,
                'rows' => $records
            ];
        } catch (Exception $Error) {
            error_log($Error->getMessage());
            return [
                'totals' => 0,
                'rows' => []
            ];
        }
    }

    public function getMedicationAttributesByRxcui($rxcui)
    {
        $response = [];

        $sth = $this->db->prepare("SELECT `ATV`, `ATN`
 								   FROM rxnsat
								  WHERE `RXCUI` = :c
								    AND `ATN` = 'RXN_AVAILABLE_STRENGTH'
								    AND `SAB` = 'RXNORM'");
        $sth->execute([':c' => $rxcui]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            $response[$result['ATN']] = $result['ATV'];
        }

        // controlled substance
	    $sth = $this->db->prepare("SELECT `ATV`, `ATN`
 								   FROM rxnsat
								  WHERE `RXCUI` = :c
								    AND `ATN` = 'DCSA'
								    AND `SAB` = 'MTHFDA'");
	    $sth->execute([':c' => $rxcui]);

	    $result = $sth->fetch(PDO::FETCH_ASSOC);
	    if ($result !== false) {
		    $response[$result['ATN']] = $result['ATV'];
	    }

        $sth = $this->db->prepare("SELECT `rxnconso`.*
								     FROM `rxnrel`
						        LEFT JOIN `rxnconso` ON `rxnconso`.`RXCUI` = `rxnrel`.`RXCUI2`
								    WHERE `rxnrel`.`RXCUI1` = :c
								      AND `rxnrel`.`RELA` = 'dose_form_of'");

        $sth->execute([':c' => $rxcui]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

	public function getMedicationAttributesByRxcuiApi($rxcui){
		$response = file_get_contents("https://rxnav.nlm.nih.gov/REST/rxcui/{$rxcui}/allProperties.json?prop=all");
		return json_decode($response, true);
	}

    public function IndexActiveIngredients()
    {
        $this->db->exec('TRUNCATE TABLE rxnconsoindex');
        $sth = $this->db->prepare("SELECT id, STR FROM rxnconso WHERE TTY = 'IN' AND SAB = 'RXNORM' GROUP BY RXCUI");
        $sth->execute();
        $records = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach ($records As $record) {
            $this->db->exec("INSERT INTO `rxnconsoindex` (`rxnid`, `STR`) VALUES ('{$record['id']}', '{$record['STR']}')");
        }
    }

    public function getMedicationInstructions($params)
    {
        $this->setInstructionModel();
        return $this->i->load($params)->all();
    }

    public function getMedicationInstruction($params)
    {
        $this->setInstructionModel();
        return $this->i->load($params)->one();
    }

    public function addMedicationInstruction($params)
    {
        $this->setInstructionModel();
        return $this->i->save($params);
    }

    public function updateMedicationInstructions($params)
    {
        $this->setInstructionModel();
        return $this->i->save($params);
    }

    public function destroyMedicationInstructions($params)
    {
        $this->setInstructionModel();
        return $this->i->save($params);
    }
}
