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
    /**
     * @var MatchaCUP
     */
    private $bc;
    /**
     * @var MatchaCUP
     */
    private $bcst;
    /**
     * @var MatchaCUP
     */
    private $bst;

	function __construct(){
        $this->ic = MatchaModel::setSenchaModel('App.model.administration.InsuranceCompany');
        $this->pi = MatchaModel::setSenchaModel('App.model.patient.Insurance');
        $this->pic = MatchaModel::setSenchaModel('App.model.patient.InsuranceCover');


        \Matcha::setAppDir(ROOT.'/modules');

        if(!isset($this->bid))
            $this->bid = \MatchaModel::setSenchaModel('Modules.billing.model.BillingInsuranceData');

        if(!isset($this->bc))
            $this->bc = \MatchaModel::setSenchaModel('Modules.billing.model.BillingCover');

        if(!isset($this->bcst))
            $this->bcst = \MatchaModel::setSenchaModel('Modules.billing.model.BillingCoverServiceType');

        if(!isset($this->bst))
            $this->bst = \MatchaModel::setSenchaModel('Modules.billing.model.Billing271ServiceType');

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
        )->leftJoin(
            ['id' => 'cover_exception_id',
                'description' => 'cover_description',
                'deductible' => 'deductible',
                'copay' => 'cover_copay',
                'notes' => 'cover_notes',
                'cover' => 'cover_exception'], 'acc_billing_covers', 'cover_exception_id', 'id'
        )->all();

        $getRecords = array_map(function($record) {
            if ($record['cover_exception'] != 0)
                $record['cover_exception'] = $record['ins_name'] . ': ' . $record['cover_exception'];

            return $record;
        }, $getRecords);

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

    public function getPatientActiveInsurancesByPid($pid) {
        $this->pi->addFilter('pid', $pid);
        $this->pi->addFilter('active', '1');
        return $this->pi->load()->leftJoin(
            [
                'code' => 'insurance_company_code',
                'name' => 'insurance_company_name',
                'external_id' => 'insurance_company_external_id',
                'global_id' => 'insurance_company_global_id'
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

	public function getPatientPrimaryInsuranceByPid($pid) {
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'P');
        $this->pi->addFilter('active', '1');
        $this->pi->addSort('id', 'DESC');
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
        $this->pi->addFilter('active', '1');
        $this->pi->addSort('id', 'DESC');
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
        $this->pi->addFilter('active', '1');
        $this->pi->addSort('id', 'DESC');
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

	public function getPatientOtherInsuranceByPid($pid) {
        $this->pi->setOrFilterProperties(['insurance_type']);
		$this->pi->addFilter('pid', $pid);
		$this->pi->addFilter('insurance_type', 'S');
		$this->pi->addFilter('insurance_type', 'C');
        $this->pi->addFilter('active', '1');
        $this->pi->addSort('id', 'DESC');
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
        $insurance_id = "";
        $cover_exception_id = "";
        $load_cover = false;

        foreach($params->filter as $i => $varName) {
            if ($varName->property == 'patient_insurance_id') {
                $patient_insurance_id = $varName->value;
            }

            if ($varName->property == 'insurance_id') {
                $insurance_id = $varName->value;
            }

            if ($varName->property == 'cover_exception_id') {
                $cover_exception_id = $varName->value;
            }

            if ($varName->property == 'load_cover') {
                $load_cover = $varName->value;
            }
        }

        $finalResults = $this->doGetPatientInsuranceCovers($patient_insurance_id, $insurance_id, $cover_exception_id, $load_cover);

        return $finalResults;
    }

    public function getInsuranceCoversByPatientInsuranceId($patient_insurance_id) {
        $_query_params = [];
        $_query_params['patientInsuranceId'] =  $patient_insurance_id;

        $finalResults = $this->doGetPatientInsuranceCovers($patient_insurance_id);

        return $finalResults;
    }

    public function doGetPatientInsuranceCovers($patient_insurance_id, $insurance_id, $cover_exception_id, $load_cover) {

        // $finalResults = [];

        // Patient insurance is being added, get default insurance covers
        if ($patient_insurance_id == 0) {
            $_query_params = [];
            $_query_params['create_uid'] =  $_SESSION['user']['id'];
            $_query_params['update_uid'] =  $_SESSION['user']['id'];
            $_query_params['create_date'] =  date("Y-m-d H:i:s");
            $_query_params['update_date'] =  date("Y-m-d H:i:s");

            $default_cover_service_types = $this->pic->sql("
                    select
                            NULL                    as id,
                            NULL                    as patient_insurance_id,
                            NULL                    as cover_id,
                            st.id                   as service_type_id,
                            st.description          as service_type_description,
                            st.department_id        as department_id,
                            dp.title                as department_title,
                            st.isDollar             as isDollar,
                            0.00                    as copay,
                            st.isDollar             as exception_isDollar,
                            0.00                    as exception_copay,
                            false                   as `exception`,
                            st.active               as active,
                            :create_uid             as create_uid,
                            :update_uid             as update_uid,
                            :create_date            as create_date,
                            :update_date            as update_date,
                            false                   as validate_copay,
                            false                   as validate_ecopay,
                            false                   as validate_service_type_status
                        from acc_billing_271_service_types st
                            left join departments dp  on st.department_id = dp.id
                        where st.active = true
                ")->all($_query_params);


            return $default_cover_service_types;
        }

        // Load patient insurance Covers
        $patient_insurance_cover_records = $this->pic->load([
            'patient_insurance_id' => $patient_insurance_id
        ])->all();



        // check if patient has insurance covers, if not, check if the selected insurance has a billing exception cover,
        // If not, then load the default covers from the default service types
        if (count($patient_insurance_cover_records) == 0) {

            // If the insurance has a billing cover, load the cover service types...
            if ($cover_exception_id != 0) {

                $cover_service_types = $this->pic->sql("
                    select
                            NULL                        as id,
                            abc.id                      as cover_id,
                            abcst.id                    as cover_service_type_id,
                            abcst.service_type_id       as service_type_id,
                            abst.description            as service_type_description,
                            abst.department_id          as department_id,
                            dp.title                    as department_title,
                            abcst.isDollar              as isDollar,
                            abcst.copay                 as copay,
                            abcst.exception_isDollar    as exception_isDollar,
                            abcst.exception_copay       as exception_copay,
                            abc.active                  as active,
                            abcst.create_uid            as create_uid,
                            abcst.update_uid            as update_uid,
                            abcst.create_date           as create_date,
                            abcst.update_date           as update_date,
                            false                       as validate_copay,
                            false                       as validate_ecopay,
                            false                       as validate_service_type_status
                        from acc_billing_covers abc
                            left join acc_billing_cover_service_types abcst  on abc.id = abcst.cover_id
                            left join acc_billing_271_service_types abst on abcst.service_type_id = abst.id
                            left join departments dp on abst.department_id = dp.id
                        where abc.active = 1
                ")->all([]);

                $finalResults = $cover_service_types;

            } else { // Insurance does not have a billing cover, load default service 271 types...

                $_query_params = [];
                $_query_params['create_uid'] =  $_SESSION['user']['id'];
                $_query_params['update_uid'] =  $_SESSION['user']['id'];
                $_query_params['create_date'] =  date("Y-m-d H:i:s");
                $_query_params['update_date'] =  date("Y-m-d H:i:s");

                $default_cover_service_types = $this->pic->sql("
                    select
                            NULL                    as id,
                            NULL                    as patient_insurance_id,
                            NULL                    as cover_id,
                            st.id                   as service_type_id,
                            st.description          as service_type_description,
                            st.department_id        as department_id,
                            dp.title                as department_title,
                            st.isDollar             as isDollar,
                            0.00                    as copay,
                            false                   as exception_isDollar,
                            0.00                    as exception_copay,
                            st.active               as active,
                            :create_uid             as create_uid,
                            :update_uid             as update_uid,
                            :create_date            as create_date,
                            :update_date            as update_date,
                            false                   as validate_copay,
                            false                   as validate_ecopay,
                            false                   as validate_service_type_status
                            
                        from acc_billing_271_service_types st
                            left join departments dp  on st.department_id = dp.id
                        where st.active = true
                ")->all($_query_params);

                $finalResults = $default_cover_service_types;
            }
        } else {
            // Verify and load existing billing covers....
            // $patient_insurance_covers = [];

            // Patient insurance has a cover exception set
            if ($cover_exception_id != 0) {

                // User clicked update cover copay and e copay
                if ($load_cover) {
                    // Load Patient Insurance covers with cover exception
                    $patient_insurance_covers = $this->pic->sql("
                        select
                                pic.id                      as id,
                                abcst.cover_id              as cover_id,
                                abcst.id                    as cover_service_type_id,
                                abcst.service_type_id       as service_type_id,
                                abst.description            as service_type_description,
                                abst.department_id          as department_id,
                                dp.title                    as department_title,
                                pic.isDollar                as isDollar,
                                abcst.copay                 as copay,
                                pic.exception_isDollar      as exception_isDollar,
                                abcst.exception_copay       as exception_copay,
                                pic.create_uid              as create_uid,
                                pic.update_uid              as update_uid,
                                pic.create_date             as create_date,
                                pic.update_date             as update_date,
                                abst.active                 as service_type_status,
                                case 
                                    when pic.copay != abcst.copay then 1
                                    else 0
                                end as validate_copay,
                                case 
                                    when pic.exception_copay != abcst.exception_copay then 1
                                    else 0
                                end as validate_ecopay,
                                case 
                                    when abst.active != 1 then 1
                                    else 0
                                end as validate_service_type_status
                            from patient_insurance_covers pic
                                left join acc_billing_cover_service_types abcst on pic.service_type_id = abcst.service_type_id
                                inner join acc_billing_271_service_types abst on pic.service_type_id = abst.id
                                left join departments dp on abst.department_id = dp.id
                            where abcst.cover_id = :exception_cover_id and pic.patient_insurance_id = :patient_insurance_id
                        ")->all([
                        ':exception_cover_id'                 => $cover_exception_id,
                        ':patient_insurance_id'               => $patient_insurance_id,
                    ]);
                } else {
                    // Load Patient Insurance covers with cover exception
                    $patient_insurance_covers = $this->pic->sql("
                        select
                                pic.id                      as id,
                                abcst.cover_id              as cover_id,
                                abcst.id                    as cover_service_type_id,
                                abcst.service_type_id       as service_type_id,
                                abst.description            as service_type_description,
                                abst.department_id          as department_id,
                                dp.title                    as department_title,
                                pic.isDollar                as isDollar,
                                pic.copay                   as copay,
                                pic.exception_isDollar      as exception_isDollar,
                                pic.exception_copay         as exception_copay,
                                pic.create_uid              as create_uid,
                                pic.update_uid              as update_uid,
                                pic.create_date             as create_date,
                                pic.update_date             as update_date,
                                abst.active                 as service_type_status,
                                case 
                                    when pic.copay != abcst.copay then 1
                                    else 0
                                end as validate_copay,
                                case 
                                    when pic.exception_copay != abcst.exception_copay then 1
                                    else 0
                                end as validate_ecopay,
                                case 
                                    when abst.active != 1 then 1
                                    else 0
                                end as validate_service_type_status
                            from patient_insurance_covers pic
                                left join acc_billing_cover_service_types abcst on pic.service_type_id = abcst.service_type_id
                                inner join acc_billing_271_service_types abst on pic.service_type_id = abst.id
                                left join departments dp on abst.department_id = dp.id
                            where abcst.cover_id = :exception_cover_id and pic.patient_insurance_id = :patient_insurance_id
                        ")->all([
                        ':exception_cover_id'                 => $cover_exception_id,
                        ':patient_insurance_id'               => $patient_insurance_id,
                    ]);
                }


            } else {
                // Load Patient Insurance covers without exception
                $patient_insurance_covers = $this->pic->sql("
                        select
                                pic.id                      as id,
                                NULL                        as cover_id,
                                pic.id                      as cover_service_type_id,
                                abst.id                     as service_type_id,
                                abst.description            as service_type_description,
                                abst.department_id          as department_id,
                                dp.title                    as department_title,
                                pic.isDollar                as isDollar,
                                pic.copay                   as copay,
                                pic.exception_isDollar      as exception_isDollar,
                                pic.exception_copay         as exception_copay,
                                pic.create_uid              as create_uid,
                                pic.update_uid              as update_uid,
                                pic.create_date             as create_date,
                                pic.update_date             as update_date,
                                abst.active                 as service_type_status,
                                0                           as validate_copay,
                                0                           as validate_ecopay,
                                case 
                                    when abst.active != 1 then 1
                                    else 0
                                end as validate_service_type_status
                            from patient_insurance_covers pic
                                inner join acc_billing_271_service_types abst on pic.service_type_id = abst.id
                                left join departments dp on abst.department_id = dp.id
                            where pic.patient_insurance_id = :patient_insurance_id
                        ")->all([
                    ':patient_insurance_id'               => $patient_insurance_id,
                ]);
            }

            $finalResults = $patient_insurance_covers;
        }

        #endregion

        return $finalResults;
    }

    public function getInsuranceCover($params) {
        return $this->pic->load($params)->one();
    }

    public function addInsuranceCover($params) {
        $records = $this->pic->save($params);

        return $records;
    }

    public function updateInsuranceCover($params) {
        $result =  $this->pic->save($params);
        return $result;
    }

    public function destroyInsuranceCover($params) {
        return $this->pic->destroy($params);
    }

    public function getPatientInsurancesTokenByPid($pid) {

        $tokens = [];

        $primary = $this->getPatientPrimaryInsuranceByPid($pid);

        if($primary !== false){
            $tokens['[PATIENT_INS_PRIMARY_NAME]'] = $primary['insurance_company_name'];
            $tokens['[PATIENT_INS_PRIMARY_CODE]'] = $primary['insurance_company_code'];
            $tokens['[PATIENT_INS_PRIMARY_SYNONYM]'] = $primary['insurance_company_synonym'];
            $tokens['[PATIENT_INS_PRIMARY_EES_NO]'] = $primary['insurance_company_ess_no'];
            $tokens['[PATIENT_INS_PRIMARY_GROUP_NO]'] = $primary['group_number'];
            $tokens['[PATIENT_INS_PRIMARY_POLICY_NO]'] = $primary['policy_number'];
            $tokens['[PATIENT_INS_PRIMARY_EXP_DATE]'] = date('F j, Y', strtotime($primary['expiration_date']));
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_NAME]'] = trim(sprintf('%s, %s %s', $primary['subscriber_surname'], $primary['subscriber_given_name'], $primary['subscriber_middle_name']));
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_FNAME]'] = $primary['subscriber_given_name'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_MNAME]'] = $primary['subscriber_middle_name'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_LNAME]'] = $primary['subscriber_surname'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_SEX]'] = $primary['subscriber_sex'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_DOB]'] = date('F j, Y', strtotime($primary['subscriber_dob']));
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_SS]'] = $primary['subscriber_ss'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_ADDRESS_1]'] = $primary['subscriber_street'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_ADDRESS_2]'] = $primary['subscriber_street_cont'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_CITY]'] = $primary['subscriber_city'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_POSTAL_CODE]'] = $primary['subscriber_postal_code'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_PHONE]'] = $primary['subscriber_phone'];
            $tokens['[PATIENT_INS_PRIMARY_SUBSCRIBER_EMPLOYER]'] = $primary['subscriber_employer'];
        }

        $secondary = $this->getPatientSecondaryInsuranceByPid($pid);

        if($secondary !== false) {
            $tokens['[PATIENT_INS_SECONDARY_NAME]'] = $secondary['insurance_company_name'];
            $tokens['[PATIENT_INS_SECONDARY_CODE]'] = $secondary['insurance_company_code'];
            $tokens['[PATIENT_INS_SECONDARY_SYNONYM]'] = $secondary['insurance_company_synonym'];
            $tokens['[PATIENT_INS_SECONDARY_EES_NO]'] = $secondary['insurance_company_ess_no'];
            $tokens['[PATIENT_INS_SECONDARY_GROUP_NO]'] = $secondary['group_number'];
            $tokens['[PATIENT_INS_SECONDARY_POLICY_NO]'] = $secondary['policy_number'];
            $tokens['[PATIENT_INS_SECONDARY_EXP_DATE]'] = date('F j, Y', strtotime($secondary['expiration_date']));
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_NAME]'] = trim(sprintf('%s, %s %s', $secondary['subscriber_surname'], $secondary['subscriber_given_name'], $secondary['subscriber_middle_name']));
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_FNAME]'] = $secondary['subscriber_given_name'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_MNAME]'] = $secondary['subscriber_middle_name'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_LNAME]'] = $secondary['subscriber_surname'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_SEX]'] = $secondary['subscriber_sex'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_DOB]'] = date('F j, Y', strtotime($secondary['subscriber_dob']));
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_SS]'] = $secondary['subscriber_ss'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_ADDRESS_1]'] = $secondary['subscriber_street'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_ADDRESS_2]'] = $secondary['subscriber_street_cont'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_CITY]'] = $secondary['subscriber_city'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_POSTAL_CODE]'] = $secondary['subscriber_postal_code'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_PHONE]'] = $secondary['subscriber_phone'];
            $tokens['[PATIENT_INS_SECONDARY_SUBSCRIBER_EMPLOYER]'] = $secondary['subscriber_employer'];
        }

        return $tokens;
    }

}