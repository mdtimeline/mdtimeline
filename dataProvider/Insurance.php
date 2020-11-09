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
include_once(ROOT .'/dataProvider/User.php');

class Insurance {

	/**
	 * @var MatchaCUP
	 */
	private $ic;
	/**
	 * @var MatchaCUP
	 */
	private $pi;	/**
	 * @var MatchaCUP
	 */
	private $pic;
	/**
	 * @var MatchaCUP
	 */
	private $bid;

	function __construct(){
        $this->ic = MatchaModel::setSenchaModel('App.model.administration.InsuranceCompany');
        $this->pi = MatchaModel::setSenchaModel('App.model.patient.Insurance');
        $this->pic = MatchaModel::setSenchaModel('App.model.patient.InsuranceCover');


        \Matcha::setAppDir(ROOT.'/modules');

        if(!isset($this->bid))
            $this->bid = \MatchaModel::setSenchaModel('Modules.billing.model.BillingInsuranceData');

        \Matcha::setAppDir(ROOT.'/app');
	}

	/** Companies */
	public function getInsuranceCompanies($params) {
		$icRecords = $this->ic->load($params)->all();
		return $icRecords;
	}

	public function getInsuranceCompany($params) {
		return $this->ic->load($params)->one();
	}

	public function addInsuranceCompany($params) {
		return $this->ic->save($params);
	}

	public function updateInsuranceCompany($params) {
		return $this->ic->save($params);
	}

	public function destroyInsuranceCompany($params) {
		return $this->ic->destroy($params);
	}

	/** Patient */

	/***
	 * @param $params
	 * @return mixed
	 */
	public function getInsurances($params) {

        $getRecords =  $this->pi->load($params)->leftJoin(
	        ['id' => 'insurance_company_id', 'name' => 'ins_name'], 'insurance_companies', 'insurance_id', 'id'
        )->leftJoin(
	        ['synonym' => 'ins_synonym'], 'acc_billing_insurance_data', 'insurance_id', 'insurance_id'
        )->all();

        return $getRecords;
	}


    /**
     * @param $params
     * @return mixed
     */
    public function getInsurance($params) {
        return $this->pi->load($params)->leftJoin(
            ['id' => 'insurance_company_id', 'name' => 'ins_name'], 'insurance_companies', 'insurance_id', 'id'
        )->leftJoin(
            ['synonym' => 'ins_synonym'], 'acc_billing_insurance_data', 'insurance_id', 'insurance_id'
        )->one();
    }

    public function getInsurancesByPid($pid) {
		$getRecords = $this->pi->load(['pid' => $pid])->leftJoin(
            ['id' => 'insurance_company_id', 'name' => 'ins_name'], 'insurance_companies', 'insurance_id', 'id'
        )->leftJoin(
            ['synonym' => 'ins_synonym'], 'acc_billing_insurance_data', 'insurance_id', 'insurance_id'
        )->all();

        return $getRecords;
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function addInsurance($params) {
        $patient_insurance = $this->pi->save($params);
        return $patient_insurance;
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function updateInsurance($params) {
            $patient_insurance = $this->pi->save($params);
            return $patient_insurance;
    }

    /**
	 * @param $params
	 * @return mixed
	 */
	public function destroyInsurance($params) {
		return $this->pi->destroy($params);
	}

	public function getInsuranceCompanyById($id){
		$this->ic->addFilter('id', $id);
		return $this->ic->load()->leftJoin(
            [
                'synonym' => 'insurance_company_synonym',
                'payer_id' => 'insurance_company_payer_id',
                'ess_no' => 'insurance_company_ess_no',
            ],
            'acc_billing_insurance_data',
            'insurance_id',
            'id'
        )->one();
	}

	public function getPatientPrimaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'P');
		return $this->pi->load()->leftJoin(
		    [
		        'code' => 'insurance_company_code',
		        'name' => 'insurance_company_name'
            ],
            'insurance_companies',
            'insurance_id',
            'id'
        )->leftJoin(
		    [
		        'synonym' => 'insurance_company_synonym',
		        'payer_id' => 'insurance_company_payer_id',
		        'ess_no' => 'insurance_company_ess_no',
            ],
            'acc_billing_insurance_data',
            'insurance_id',
            'id'
        )->one();
	}

	public function getPatientSecondaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'S');
		return $this->pi->load()->leftJoin(
            [
                'code' => 'insurance_company_code',
                'name' => 'insurance_company_name'
            ],
            'insurance_companies',
            'insurance_id',
            'id'
        )->one();
	}

	public function getPatientComplementaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'C');
		return $this->pi->load()->leftJoin(
            [
                'code' => 'insurance_company_code',
                'name' => 'insurance_company_name'
            ],
            'insurance_companies',
            'insurance_id',
            'id'
        )->one();
	}

    function arraySort($records, $field, $reverse=true)
    {
        $hash = array();

        foreach($records as $record)
        {
            $hash[$record[$field]] = $record;
        }

        ($reverse)? krsort($hash) : ksort($hash);

        $records = array();

        foreach($hash as $record)
        {
            $records []= $record;
        }

        return $records;
    }

    public function indexArray($newArray)
    {
        $i = 0;
        $na = [];

        foreach ($newArray as $row)
        {
            $na[$i] = $row;
            $i += 1;
        }

        return $na;
    }




    /**
     * @param $params
     * @return mixed
     *
     * PATIENT INSURANCE COVER - INSURANCE / EXCEPTION
     */
    public function getInsuranceCovers($params) {

        $patient_insurance_id = "";

        foreach($params->filter as $i => $varName) {
            if ($varName->property == 'patient_insurance_id') {
                $patient_insurance_id = $varName->value;
            }
        }

        $finalResults = $this->doGetPatientInsuranceCovers($patient_insurance_id);

        return $finalResults;
    }

    public function getInsuranceCoversByPatientInsuranceId($patient_insurance_id) {
        $_query_params = [];
        $_query_params['patientInsuranceId'] =  $patient_insurance_id;

        $finalResults = $this->doGetPatientInsuranceCovers($patient_insurance_id);

        return $finalResults;
    }

    public function doGetPatientInsuranceCovers($patient_insurance_id) {

        if ($patient_insurance_id === 0) {

            #region Seguro Paciente NO SE HA ANADIDO. POR ENDE Patient_Insurance_Id === 0

            $_query_params = [];
            $_query_params['create_uid'] =  $_SESSION['user']['id'];
            $_query_params['update_uid'] =  $_SESSION['user']['id'];
            $_query_params['create_date'] =  date("Y-m-d H:i:s");
            $_query_params['update_date'] =  date("Y-m-d H:i:s");

            $sqlSelect = "select
                              ''                 as id, 
                              ''                 as patient_insurance_id,
                            st.id                as service_type_id,
                            st.description       as service_type_description,
                            st.department_id     as department_id,
                            dp.title             as department_title,
                            st.isDollar          as isDollar,
                            0.00                 as copay,
                            st.isDollar          as exception_isDollar,
                            0.00                 as exception_copay,
                            false                as exception,
                            st.active            as active,
                            :create_uid          as create_uid,
                            :update_uid          as update_uid,  
                            :create_date         as create_date,
                            :update_date         as update_date
                        from acc_billing_271_service_types st
                            left join departments dp  on st.department_id = dp.id
                        where st.active = true  ";

            $finalResults =  $this->pic->sql($sqlSelect)->all($_query_params);

            foreach ($finalResults as &$finalResult){
                $finalResult['id'] = null;
                $finalResult['patient_insurance_id'] = null;
            }

            #endregion

            return $finalResults;
        }

        #region Validar el Template de Cubiertas con las del Paciente (Para anadir nuevas y eliminar Inactivas)

        $_query_params = [];
        $_query_params['patientInsuranceId_0'] =  $patient_insurance_id;
        $_query_params['patientInsuranceId_1'] =  $patient_insurance_id;

        $sqlSelect = "
                select
                    pic_id, 
                    service_type_id,
                    department_id,
                    department_title,
                    service_type_description,
                    max(isDollar) as isDollar,
                    max(copay) as copay,
                    max(exception_isDollar) as exception_isDollar,
                    max(exception_copay) as exception_copay,
                    max(exception) as exception,
                    max(active) as active,
                    max(valueExist) as valueExist,
                    max(update_date) as update_date
                from
                    (
                        select
                            null                 as pic_id,
                            st.id                as service_type_id,
                            st.department_id     as department_id,
                            dp.title             as department_title,
                            st.description       as service_type_description,
                            st.isDollar          as isDollar,
                            0.00                 as copay,
                            st.isDollar          as exception_isDollar,
                            0.00                 as exception_copay,
                            false                as exception,
                            true                 as active,
                            'add'                as valueExist,
                            now()                as update_date
                        from acc_billing_271_service_types st
                            join departments dp  on st.department_id = dp.id
                        where st.active = true and 
                              st.id not in ( select service_type_id 
										     from patient_insurance_covers 
                                             where patient_insurance_id = :patientInsuranceId_0
                                             group by service_type_id )
                        
                        union
                        
                        select
                            pic.id               as pic_id,
                            st.id                as service_type_id,
                            st.department_id     as department_id,
                            dp.title             as department_title,
                            st.description       as service_type_description,
                            st.isDollar          as isDollar,
                            pic.copay,
                            st.isDollar          as exception_isDollar,
                            pic.exception_copay,
                            pic.exception, 
                            pic.active, 
                            case st.active 
                            when true then 'load'
                            when false then 'delete'
                            end as valueExist,
                            pic.update_date as update_date
                        from patient_insurance_covers pic
                            join acc_billing_271_service_types st on st.id = pic.service_type_id
                            join departments dp  on st.department_id = dp.id
                        where patient_insurance_id = :patientInsuranceId_1 
                        
                    ) t1
                    
                group by
                    service_type_id,
                    department_id,
                    department_title,
                    service_type_description ";

        $results = $this->pic->sql($sqlSelect)->all($_query_params);


        foreach ($results as $result) {

            if ($result['valueExist'] == "delete") {

                $service_type_id = $result['service_type_id'];

                $_delete_params = [];
                $_delete_params['patientInsuranceId'] =  $patient_insurance_id;
                $_delete_params['serviceTypeId'] =  $service_type_id;

                $sqlDelete = "delete from patient_insurance_covers where patient_insurance_id = :patientInsuranceId and service_type_id = :serviceTypeId ";

                $this->pic->sql($sqlDelete)->all($_delete_params);

            }


            if ($result['valueExist'] == "load") {

                $_add_params = (object) array(
                    'id' =>  $result['pic_id'],
                    'patient_insurance_id' =>  $patient_insurance_id,
                    'service_type_id' =>  $result['service_type_id'],
                    'isDollar' => $result['isDollar'],
                    'copay' =>  $result['copay'],
                    'exception_isDollar' => $result['exception_isDollar'],
                    'exception_copay' => $result['exception_copay'],
                    'exception' => $result['exception'],
                    'active' =>  $result['active'],
                    'create_uid' =>  $_SESSION['user']['id'],
                    'update_uid' =>  $_SESSION['user']['id'],
                    'create_date' =>  date("Y-m-d H:i:s"),
                    'update_date' =>  date("Y-m-d H:i:s"));

                $this->pic->save($_add_params);
            }

            if ($result['valueExist'] == "add") {

                $_add_params = (object) array(
                    'id' =>  null,
                    'patient_insurance_id' =>  $patient_insurance_id,
                    'service_type_id' =>  $result['service_type_id'],
                    'isDollar' => $result['isDollar'],
                    'copay' =>  $result['copay'],
                    'exception_isDollar' => $result['exception_isDollar'],
                    'exception_copay' => $result['exception_copay'],
                    'exception' => $result['exception'],
                    'active' =>  $result['active'],
                    'create_uid' =>  $_SESSION['user']['id'],
                    'update_uid' =>  $_SESSION['user']['id'],
                    'create_date' =>  date("Y-m-d H:i:s"),
                    'update_date' =>  date("Y-m-d H:i:s"));

                $this->pic->save($_add_params);
            }

        }
        #endregion

        #region Cargar la Cubierta ya actualizada.

        $_query_params = [];
        $_query_params['patientInsuranceId'] =  $patient_insurance_id;

        $sqlSelect = "select
                            pic.id                   as id, 
                            pic.patient_insurance_id as patient_insurance_id,
                            pic.service_type_id      as service_type_id,
                            pic.isDollar             as isDollar,
                            pic.copay                as copay,
                            pic.exception_isDollar   as exception_isDollar,
                            pic.exception_copay      as exception_copay,
                            pic.exception            as exception,
                            pic.active               as active,
                            pic.create_uid           as create_uid,
                            pic.update_uid           as update_uid,
                            pic.create_date          as create_date,
                            pic.update_date          as update_date,
                            st.department_id         as department_id,
                            dp.title                 as department_title,
                            st.description           as service_type_description
                        from patient_insurance_covers pic
                            join acc_billing_271_service_types st on st.id = pic.service_type_id
                            join departments dp  on st.department_id = dp.id
                        where patient_insurance_id = :patientInsuranceId ";

        $finalResults = $this->pic->sql($sqlSelect)->all($_query_params);

        #endregion

        return $finalResults;


    }

    public function getInsuranceCover($params) {
        return $this->pic->load($params)->one();
    }

    public function addInsuranceCover($params) {
        return $this->pic->save($params);
    }

    public function updateInsuranceCover($params) {
        $result =  $this->pic->save($params);
        return $result;
    }

    public function destroyInsuranceCover($params) {
        return $this->pic->destroy($params);
    }





}