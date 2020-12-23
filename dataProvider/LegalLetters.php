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
}


