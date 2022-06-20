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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class ReferringProviders {

	/**
	 * @var MatchaCUP
	 */
	private $r;
	/**
	 * @var MatchaCUP
	 */
	private $rf;
	/**
	 * @var MatchaCUP
	 */
	private $rb;

	function __construct(){
            $this->r = MatchaModel::setSenchaModel('App.model.administration.ReferringProvider');
            $this->rf = MatchaModel::setSenchaModel('App.model.administration.ReferringProviderFacility');
            $this->rb = MatchaModel::setSenchaModel('App.model.administration.ReferringProviderInsuranceBlacklist');
	}

	public function getReferringProviders($params){
		return $this->r->load($params)->all();
	}

	public function getReferringProvider($params){
		// $this->getFacilities will try to find the facilities for the record
		return $this->getFacilities($this->r->load($params)->one());
	}

	public function addReferringProvider($params){
		return $this->r->save($params);
	}

	public function updateReferringProvider($params){
		return $this->r->save($params);
	}

	public function deleteReferringProvider($params){
		return $this->r->destroy($params);
	}

	public function getReferringProviderFacilities($params){
		return $this->rf->load($params)->all();
	}

	public function getReferringProviderFacility($params){
		return $this->rf->load($params)->one();
	}

	public function addReferringProviderFacility($params){
		return $this->rf->save($params);
	}

	public function updateReferringProviderFacility($params){
		return $this->rf->save($params);
	}

	public function deleteReferringProviderFacility($params){
		return $this->rf->destroy($params);
	}

	public function getReferringProviderInsuranceBlacklists($params){
        return $this->rb->load($params)
            ->leftJoin(
                [
                    'title' => 'specialty_name',
                ], 'specialties', 'specialty_id', 'id'
            )->leftJoin(
                [
                    'name' => 'insurance_name',
                ], 'insurance_companies', 'insurance_id', 'id'
            )->all();
	}

	public function getReferringProviderInsuranceBlacklist($params){
        return $this->rb->load($params)
            ->leftJoin(
                [
                    'title' => 'specialty_name',
                ], 'specialties', 'specialty_id', 'id'
            )->leftJoin(
                [
                    'name' => 'insurance_name',
                ], 'insurance_companies', 'insurance_id', 'id'
            )->one();
	}

	public function addReferringProviderInsuranceBlacklist($params){
		return $this->rb->save($params);
	}

	public function updateReferringProviderInsuranceBlacklist($params){
		return $this->rb->save($params);
	}

	public function deleteReferringProviderInsuranceBlacklist($params){
		return $this->rb->destroy($params);
	}

	public function getReferringProviderById($id, $include_facilities = true){
		return $this->getReferringProvider($id);
	}

	public function getFacilities($record){
		$filter = new stdClass();
        $filter->filter[0] = new stdClass();
        $filter->filter[0]->property = 'referring_provider_id';

		if(isset($record['data']) && $record['data'] !== false){
            $filter->filter[0]->value = $record['data']['id'];
			$record['data']['facilities'] = $this->rf->load($filter)->all();
		}elseif($record !== false){
            $filter->filter[0]->value = $record['id'];
			$record['facilities'] = $this->rf->load($filter)->all();
		}
		return $record;
	}


    /**
     * @param stdClass $params
     * @return array
     */
    public function referringPhysicianLiveSearch(stdClass $params)
    {
        $conn = Matcha::getConn();
        $whereValues = [];
        $where = [];

        if(!isset($params->query)){
	        return [
		        'totals' => 0,
		        'rows' => []
	        ];
        }

        $queries = explode(',', $params->query);
        foreach($queries as $index => $query){
            $query = trim($query);
            if($query == '') continue;
            $where[] = " (npi = :npi{$index} OR fname LIKE :fname{$index} OR lname LIKE :lname{$index} OR organization_name LIKE :organization_name{$index} OR email LIKE :email{$index})";

            $whereValues[':fname'.$index] = $query . '%';
            $whereValues[':lname'.$index] = $query . '%';
            $whereValues[':organization_name'.$index] = '%' . $query . '%';
            $whereValues[':npi'.$index] = $query;
            $whereValues[':email'.$index] = $query;
        }


        if(empty($where)){
	        return [
		        'totals' => 0,
		        'rows' => []
	        ];
        }

        $sth = $conn->prepare('SELECT * FROM referring_providers WHERE ' . implode(' AND ', $where) . ' LIMIT 300');
        $sth->execute($whereValues);
        $providers = $sth->fetchAll(PDO::FETCH_ASSOC);
        return [
            'totals' => count($providers),
            'rows' => array_slice($providers, $params->start, $params->limit)
        ];

    }

    public function getReferringTokenById($referring_id){

        $referring = $this->getReferringProviderById($referring_id);
        $tokens = [];

        if($referring === false){
            return $tokens;
        }

        $tokens['[REFERRING_ID]'] = $referring['id'];
        $tokens['[REFERRING_NAME]'] = trim(sprintf('%s, %s %s', $referring['lname'],$referring['fname'],$referring['mname']));
        $tokens['[REFERRING_TITLE]'] = $referring['title'];
        $tokens['[REFERRING_FIRST_NAME]'] = $referring['fname'];
        $tokens['[REFERRING_LAST_NAME]'] = $referring['lname'];
        $tokens['[REFERRING_MIDDLE_NAME]'] = $referring['mname'];
        $tokens['[REFERRING_NPI]'] = $referring['npi'];
        $tokens['[REFERRING_LIC]'] = $referring['lic'];
        $tokens['[REFERRING_EMAIL]'] = $referring['email'];
        $tokens['[REFERRING_PHONE_NUMBER]'] = $referring['phone_number'];
        $tokens['[REFERRING_FAX_NUMBER]'] = $referring['fax_number'];
        $tokens['[REFERRING_CEL_NUMBER]'] = $referring['cel_number'];

        return $tokens;

    }

    public function isReferringProviderBlacklistedByPid($pid, $npi, $specialty_id){

        $blacklists = $this->rb->load(['npi' => $npi, 'specialty_id' => $specialty_id])->all();

        if(count($blacklists) === 0){
            return false;
        }

        include_once(ROOT . '/dataProvider/Insurance.php');
        $Insurance = new Insurance();
        $active_insurances = $Insurance->getPatientActiveInsurancesByPid($pid);

        foreach($blacklists as $blacklist){
            foreach($active_insurances as $active_insurance){
                if($active_insurance['insurance_id'] == $blacklist['insurance_id']){
                    return true;
                }
            }
        }

        return false;
    }

    public function isReferringProviderBlacklistedByInsurencaId($insurance_ids, $npi, $specialty_id){

        $this->rb->addFilter('npi', $npi);
        $this->rb->addFilter('specialty_id', $specialty_id);

        if(is_array($insurance_ids)){
            $this->rb->setOrFilterProperties(['insurance_id']);
            foreach($insurance_ids as $insurance_id){
                $this->rb->addFilter('insurance_id', $insurance_id);
            }
        }else{
            $this->rb->addFilter('insurance_id', $insurance_ids);
        }

        return $this->rb->load()->one() !== false;
    }
}
