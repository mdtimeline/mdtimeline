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

include_once(ROOT . '/classes/Arrays.php');

class SnomedCodes {

	private $conn;

	function __construct() {
		$this->conn = MatchaHelper::getConn();
	}

	public function liveProblemCodeSearch($params) {

		$sql = "SELECT ConceptId, FullySpecifiedName, OCCURRENCE
			     FROM sct_concepts
		   RIGHT JOIN sct_problem_list ON sct_concepts.ConceptId = sct_problem_list.SNOMED_CID
	            WHERE sct_concepts.ConceptStatus = '0'
	              AND (sct_concepts.FullySpecifiedName LIKE :c1
	              OR sct_concepts.ConceptId LIKE :c2)
	         ORDER BY sct_problem_list.OCCURRENCE DESC";

		$sth = $this->conn->prepare($sql);
		$sth->execute([':c1' => '%'.$params->query.'%', ':c2' => $params->query.'%']);
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
		return [
			'totals' => count($results),
		    'data' => array_slice($results, $params->start, $params->limit)
		];
	}

	public function liveProcedureCodeSearch($params) {

		$sql = "SELECT ConceptId, FullySpecifiedName, Occurrence
			     FROM sct_procedure_list
	            WHERE FullySpecifiedName 	LIKE :c1
	               OR ConceptId 			LIKE :c2
	         ORDER BY Occurrence DESC";

		$sth = $this->conn->prepare($sql);
		$sth->execute([':c1' => '%'.$params->query.'%', ':c2' => $params->query.'%']);
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
		return [
			'totals' => count($results),
		    'data' => array_slice($results, $params->start, $params->limit)
		];
	}

	public function liveCodeSearch($params) {

		$sql = "SELECT ConceptId, FullySpecifiedName
			     FROM sct_concepts
	            WHERE sct_concepts.ConceptStatus = '0'
	              AND sct_concepts.FullySpecifiedName LIKE :c1
	              OR sct_concepts.ConceptId LIKE :c2";

		$sth = $this->conn->prepare($sql);
		$sth->execute([':c1' => '%'.$params->query.'%', ':c2' => $params->query.'%']);
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);

		return [
			'totals' => count($results),
		    'data' => array_slice($results, $params->start, $params->limit)
		];
	}

	public function updateLiveProcedureCodeSearch($params) {
		$sql = "UPDATE sct_procedure_list
				   SET Occurrence = '{$params->Occurrence}'
			     WHERE ConceptId = '{$params->ConceptId}'";
		$this->conn->exec($sql);
		return $params;
	}

	public function updateLiveProblemCodeSearch($params) {

		$sql = "UPDATE sct_problem_list
				   SET OCCURRENCE = '{$params->OCCURRENCE}'
			     WHERE SNOMED_CID = '{$params->ConceptId}'";
		$this->conn->exec($sql);
		return $params;
	}

	public function getSnomedTextByConceptId($conceptId) {
		$sql = "SELECT `FullySpecifiedName` FROM `sct_concepts` WHERE `ConceptId` = '$conceptId'";
		$sth = $this->conn->prepare($sql);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return isset($result) && $result != false ? $result['FullySpecifiedName'] : '';
	}

	public function getMetalAllergiesCodes($params){
		$metals_allergies = [
			'105829002', // Actinium
			'105830007', // Aluminum
			'105880001', // Americium
			'105831006', // Antimony
			'105832004', // Barium
			'105833009', // Beryllium
			'105835002', // Bismuth
			'105853008', // Calcium
			'105834003', // Californium
			'290033005', // Cerium
			'105852003', // Cesium
			'105839008', // Chromium
			'105854002', // Cobalt
			'105837005', // Copper
			'429591003', // Europium
			'105879004', // Gadolinium
			'105841009', // Gallium
			'105838000', // Germanium
			'105856000', // Gold
			'105857009', // Hafnium
			'105855001', // Indium
			'105861003', // Iridium
			'105840005', // Iron
			'414569007', // Lanthanum
			'105864006', // Lithium
			'105858004', // Magnesium
			'105859007', // Manganese
			'425620007', // Metal
			'5160007',   // Metallic compound
			'105860002', // Molybdenum
			'105844001', // Nickel
			'429310004', // Niobium
			'105862005', // Osmium
			'105870000', // Palladium
			'105863000', // Platinum
			'105845000', // Potassium
			'105846004', // Radium
			'395999005', // Rhenium
			'105877002', // Rhodium
			'105869001', // Rubidium
			'105881002', // Ruthenium
			'419295005', // Samarium
			'105847008', // Silver
			'105865007', // Sodium
			'105868009', // Strontium
			'105866008', // Tantalum
			'105873003', // Technetium
			'105827000', // Tellurium
			'105867004', // Thorium
			'105876006', // Titanium
			'303702002', // Transuranic
			'105874009', // Tungsten
			'105850006', // Uranium
			'105875005', // Vanadium
			'105882009', // Ytterbium
			'105851005', // Zinc
			'105878007'  // Zirconium
		];
		$metals_allergies = implode(',', $metals_allergies);
		$sql = "SELECT ConceptId, FullySpecifiedName, 'SNOMEDCT' as CodeType
			     FROM sct_concepts
	            WHERE sct_concepts.ConceptStatus = '0'
	              AND sct_concepts.ConceptId IN($metals_allergies)";
		$sth = $this->conn->prepare($sql);
		$sth->execute();
		$results = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $results;
	}

}
