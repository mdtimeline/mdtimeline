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


include_once(ROOT . '/dataProvider/ACL.php');
include_once(ROOT . '/dataProvider/i18nRouter.php');

class Navigation
{
	/**
	 * @var \ACL
	 */
	private $ACL;
	private $i18n;

	function __construct()
	{
		$this->ACL = new ACL();
		$this->i18n = i18nRouter::getTranslation();
	}

	private function i18n($w)
    {
		return isset($this->i18n[$w]) ? $this->i18n[$w] : '*' . $w . '*';
	}

    /***
     * Renders the items of the navigation panel
     * Default Nav Data
     * @return array
     */
	public function getNavigation()
	{
		$nav = [];
		$nav[] = [
			'text' => $this->i18n('dashboard'),
			'leaf' => true,
			'cls' => 'file',
			'iconCls' => 'icoDash',
			'id' => 'App.view.dashboard.Dashboard'
		];

		if($this->ACL->hasPermission('access_floor_plan_panel')){
			$nav[] = [
				'text' => $this->i18n('area_floor_plan'),
				'leaf' => true,
				'cls' => 'file',
				'iconCls' => 'icoZoneAreas',
				'id' => 'App.view.areas.FloorPlan'
			];
		}

		if($this->ACL->hasPermission('access_pool_areas_panel')){
			$nav[] = [
				'text' => $this->i18n('patient_pool_areas'),
				'leaf' => true,
				'cls' => 'file',
				'iconCls' => 'icoPoolArea16',
				'id' => 'App.view.areas.PatientPoolAreas'
			];
		}

		// Patient Folder
		$patient = [
			'text' => $this->i18n('patient'),
			'cls' => 'folder',
			'expanded' => true,
			'iconCls' => 'icoLogo',
			'id' => 'patient'
		];
		if($this->ACL->hasPermission('add_patient')){
			$patient['children'][] = [
				'text' => $this->i18n('new_patient'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.NewPatient'
			];
		}
		//if($this->ACL->hasPermission('add_patient')){
			$patient['children'][] = [
				'text' => 'Patient List',
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.Search'
			];
		//}
		if($this->ACL->hasPermission('access_patient_summary')){
			$patient['children'][] = [
				'text' => $this->i18n('patient_summary'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.Summary'
			];
		}
		if($this->ACL->hasPermission('access_patient_visits')){
			$patient['children'][] = [
				'text' => $this->i18n('visits_history'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.Visits'
			];
		}
		if($this->ACL->hasPermission('access_encounters')){
			$patient['children'][] = [
				'text' => $this->i18n('encounter'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.Encounter'
			];
		}
		if($this->ACL->hasPermission('access_visit_checkout')){
			$patient['children'][] = [
				'text' => $this->i18n('visit_checkout'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.patient.VisitCheckout'
			];
		}
		array_push($nav, $patient);

		// Billing Manager Folder
		array_push($nav, [
			'text' => $this->i18n('billing_manager'),
			'cls' => 'folder',
			'expanded' => true,
			'id' => 'billing',
			'iconCls' => 'icoLogo',
			'children' => [
				[
					'text' => $this->i18n('payment'),
					'leaf' => true,
					'cls' => 'file',
					'id' => 'App.view.fees.Payments'
				], [
					'text' => $this->i18n('billing'),
					'leaf' => true,
					'cls' => 'file',
					'id' => 'App.view.fees.Billing'
				]
			]
		]);

		// Administration Folder
		$admin = [
			'text' => $this->i18n('administration'),
			'cls' => 'folder',
			'expanded' => true,
			'iconCls' => 'icoLogo',
			'id' => 'administration'
		];
		if($this->ACL->hasPermission('access_global_settings')){
			$admin['children'][] = [
				'text' => $this->i18n('global_settings'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Globals'
			];
		}
		if($this->ACL->hasPermission('access_users')){
			$admin['children'][] = [
				'text' => $this->i18n('users'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Users'
			];
		}
		if($this->ACL->hasPermission('access_practice')){
			$admin['children'][] = [
				'text' => $this->i18n('practice'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.practice.Practice'
			];
		}
		if($this->ACL->hasPermission('access_data_manager')){
			$admin['children'][] = [
				'text' => $this->i18n('data_manager'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.DataManager'
			];
		}
        if($this->ACL->hasPermission('access_cronjob_administration')){
            $admin['children'][] = [
                'text' => $this->i18n('cronjob_manager'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.CronJob'
            ];
        }
		if($this->ACL->hasPermission('access_preventive_care')){
			$admin['children'][] = [
				'text' => $this->i18n('decision_support'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.DecisionSupport'
			];
		}
		if($this->ACL->hasPermission('access_floor_plans')){
			$admin['children'][] = [
				'text' => $this->i18n('pool_areas'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.PoolAreas'
			];
			$admin['children'][] = [
				'text' => $this->i18n('floor_areas'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.FloorPlans'
			];
		}
		if($this->ACL->hasPermission('access_roles')){
			$admin['children'][] = [
				'text' => $this->i18n('roles'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Roles'
			];
		}
		if($this->ACL->hasPermission('access_layouts')){
			$admin['children'][] = [
				'text' => $this->i18n('layouts'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Layout'
			];
		}
		if($this->ACL->hasPermission('access_lists')){
			$admin['children'][] = [
				'text' => $this->i18n('lists'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Lists'
			];
		}
		if($this->ACL->hasPermission('access_documents')){
			$admin['children'][] = [
				'text' => $this->i18n('documents'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Documents'
			];
		}
		if($this->ACL->hasPermission('access_admin_hl7')){
			$admin['children'][] = [
				'text' => $this->i18n('hl7'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.HL7'
			];
		}
		if($this->ACL->hasPermission('access_admin_external_data')){
			$admin['children'][] = [
				'text' => $this->i18n('external_data_loads'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.ExternalDataLoads'
			];
		}
		if($this->ACL->hasPermission('access_admin_applications')){
			$admin['children'][] = [
				'text' => $this->i18n('applications'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Applications'
			];
		}
		if($this->ACL->hasPermission('access_admin_modules')){
			$admin['children'][] = [
				'text' => $this->i18n('modules'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Modules'
			];
		}

		if($this->ACL->hasPermission('access_admin_encryption')){
			$admin['children'][] = [
				'text' => $this->i18n('encryption'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.Encryption'
			];
		}

		if($this->ACL->hasPermission('access_admin_patients_export')){
			$admin['children'][] = [
				'text' => $this->i18n('patients_export'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.administration.DataPortability'
			];
		}
        if($this->ACL->hasPermission('admin_access_networks')){
            $admin['children'][] = [
                'text' => $this->i18n('network_ip_access'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.IpAccess'
            ];
        }
        if($this->ACL->hasPermission('admin_access_transaction_log')){
            $admin['children'][] = [
                'text' => $this->i18n('transaction_log'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.TransactionLog'
            ];
        }

        // TODO change permission
        if($this->ACL->hasPermission('admin_access_transaction_log')){
            $admin['children'][] = [
                'text' => $this->i18n('file_systems'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.FileSystems'
            ];
        }
        if($this->ACL->hasPermission('access_backup_settings')){
            $admin['children'][] = [
                'text' => $this->i18n('backups'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.Backup'
            ];
        }
        if($this->ACL->hasPermission('access_encounter_templates')){
            $admin['children'][] = [
                'text' => $this->i18n('encounter_templates'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.EncounterTemplates'
            ];
        }
        if($this->ACL->hasPermission('access_email_templates')){
            $admin['children'][] = [
                'text' => $this->i18n('email_templates'),
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.EmailTemplates'
            ];
        }
        if($this->ACL->hasPermission('access_admin_measure_calculation')){
            $admin['children'][] = [
                'text' => 'Measure Calculation',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.MeasureCalculation'
            ];
        }
        if($this->ACL->hasPermission('access_admin_printer')){
            $admin['children'][] = [
                'text' => 'Printers',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.Printers'
            ];
        }
        if($this->ACL->hasPermission('access_admin_content_management')){
            $admin['children'][] = [
                'text' => 'Content Management',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.ContentManagement'
            ];
        }

        if($this->ACL->hasPermission('access_admin_snippets_manager')){
            $admin['children'][] = [
                'text' => 'Snippets Manager',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.SnippetsManager'
            ];
        }

        if($this->ACL->hasPermission('access_admin_legal_letters')){
            $admin['children'][] = [
                'text' => 'Legal Letters',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.LegalLetters'
            ];
        }

        if($this->ACL->hasPermission('access_admin_pdf_forms')){
            $admin['children'][] = [
                'text' => 'PDF Forms',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.PdfForms'
            ];
        }

        if($this->ACL->hasPermission('access_admin_updates')){
            $admin['children'][] = [
                'text' => 'Update',
                'leaf' => true,
                'cls' => 'file',
                'id' => 'App.view.administration.Update'
            ];
        }

		if(isset($admin['children']) && count($admin['children']) > 0) array_push($nav, $admin);


		// *************************************************************************************
		// Miscellaneous Folder
		// *************************************************************************************
		$misc = [
			'text' => $this->i18n('miscellaneous'),
			'cls' => 'folder',
			'expanded' => true,
			'iconCls' => 'icoLogo',
			'id' => 'miscellaneous'
		];

		if($this->ACL->hasPermission('amendments_access')){
			$misc['children'][] = [
				'text' => $this->i18n('amendments'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.miscellaneous.Amendments'
			];
		}

		if($this->ACL->hasPermission('access_address_book')){
			$misc['children'][] = [
				'text' => $this->i18n('address_book'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.miscellaneous.AddressBook'
			];
		}

		if($this->ACL->hasPermission('access_office_notes')){
			$misc['children'][] = [
				'text' => $this->i18n('office_notes'),
				'leaf' => true,
				'cls' => 'file',
				'id' => 'App.view.miscellaneous.OfficeNotes'
			];
		}

		$misc['children'][] = [
			'text' => $this->i18n('my_account'),
			'leaf' => true,
			'cls' => 'file',
			'id' => 'App.view.miscellaneous.MyAccount'
		];

		if(isset($misc['children']) && count($misc['children']) > 0) array_push($nav, $misc);


		// *************************************************************************************
		// Reports Folder
		// *************************************************************************************
		$reports = [
			'text' => $this->i18n('reports'),
			'cls' => 'folder',
			'expanded' => true,
			'iconCls' => 'icoLogo',
			'id' => 'reports',
			'children' => []
		];
		array_push($nav, $reports);



		return $nav;

	}

}
