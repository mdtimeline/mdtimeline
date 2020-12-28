<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 9/8/18
 * Time: 8:29 PM
 */

class LegalLetters
{

	/**
	 * @var MatchaCUP
	 */
	private $l;

	function __construct(){
		$this->l = MatchaModel::setSenchaModel('App.model.administration.LegalLetter');
	}

	public function getLegalLetters($params){
		return $this->l->load($params)->all();
	}

	public function getLegalLetter($params){
		return $this->l->load($params)->one();
	}

	public function addLegalLetter($params){
		return $this->l->save($params);
	}

	public function updateLegalLetter($params){
		return $this->l->save($params);
	}

	public function destroyLegalLetter($params){
		return $this->l->destroy($params);
	}

	public function getLegalLettersToSignByPid($pid, $facility_id, $workflow){

	    $sql = "SELECT pl.* FROM (
                    SELECT	letter.*,
                            MIN(datediff(CURDATE(), signature.signature_date)) AS signed_days_ago
                    FROM legal_letters AS letter
                    LEFT JOIN legal_letters_signatures AS signature ON
                        signature.letter_id = letter.id AND
                        letter.workflow = :workflow AND
                        signature.pid = :pid AND 
                        (letter.facility_id = :facility_id OR letter.facility_id = '0')
                    GROUP BY letter.id
                ) as pl
                WHERE pl.days_valid_for <= pl.signed_days_ago OR pl.signed_days_ago IS NULL";

	    return $this->l->sql($sql)->all([':facility_id'=> $facility_id,':pid' => $pid, ':workflow' => $workflow]);
	}
}


