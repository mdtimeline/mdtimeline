/**
 * Created by ernesto on 10/29/14.
 */
module.exports = function(grunt){

	grunt.initConfig({
		concat: {
			options: {
				stripBanners: true
			},
			dist: {
				src: [
//					'app/ux/Overrides.js',
//					'app/ux/VTypes.js',
					'lib/extjs-4.2.1/examples/ux/SlidingPager.js',
					'lib/extjs-4.2.1/examples/ux/PreviewPlugin.js',
					'lib/extjs-4.2.1/examples/ux/form/SearchField.js',
					'lib/extjs-4.2.1/examples/ux/statusbar/StatusBar.js',
					'lib/extjs-4.2.1/examples/ux/LiveSearchGridPanel.js',
					'lib/extjs-4.2.1/examples/ux/IFrame.js',
					'lib/extjs-4.2.1/examples/ux/DataTip.js',
					'app/ux/RatingField.js',
					'app/ux/grid/LiveSearchGridPanel.js',
					'app/ux/grid/Printer.js',
					'app/ux/ActivityMonitor.js',
					'app/ux/AbstractPanel.js',
					'app/ux/LiveCPTSearch.js',
					'app/ux/LiveICDXSearch.js',
					'app/ux/LiveImmunizationSearch.js',
					'app/ux/LiveMedicationSearch.js',
					'app/ux/LiveLabsSearch.js',
					'app/ux/LiveCDTSearch.js',
					'app/ux/LiveRXNORMAllergySearch.js',
					'app/ux/LivePatientSearch.js',
					'app/ux/LiveSigsSearch.js',
					'app/ux/LiveUserSearch.js',
					'app/ux/NodeDisabled.js',
					'app/ux/PhotoIdWindow.js',
					'app/ux/PatientEncounterCombo.js',
					'app/ux/RenderPanel.js',
					'app/ux/form/fields/Help.js',
					'app/ux/form/fields/Checkbox.js',
					'app/ux/form/fields/ColorPicker.js',
					'app/ux/form/fields/Currency.js',
					'app/ux/form/fields/CustomTrigger.js',
					'app/ux/form/fields/DateTime.js',
					'app/ux/form/fields/MultiText.js',
					'app/ux/form/fields/plugin/BadgeText.js',
					'app/ux/form/fields/plugin/ReadOnlyLabel.js',
					'app/ux/form/AdvanceForm.js',
					'app/ux/form/fields/Percent.js',
					'app/ux/ManagedIframe.js',
					'app/ux/form/Panel.js',
					'app/ux/grid/DeleteColumn.js',
					'app/ux/grid/EventHistory.js',
					'app/ux/combo/ActiveFacilities.js',
					'app/ux/grid/RowFormEditor.js',
					'app/ux/combo/ActiveInsurances.js',
					'app/ux/combo/ActiveProviders.js',
					'app/ux/combo/Allergies.js',
					'app/ux/combo/AllergiesAbdominal.js',
					'app/ux/combo/AllergiesLocation.js',
					'app/ux/combo/AllergiesSeverity.js',
					'app/ux/combo/AllergiesTypes.js',
					'app/ux/combo/Authorizations.js',
					'app/ux/combo/BillingFacilities.js',
					'app/ux/combo/CalendarCategories.js',
					'app/ux/combo/CalendarStatus.js',
					'app/ux/combo/CodesTypes.js',
					'app/ux/combo/Combo.js',
					'app/ux/combo/CVXManufacturers.js',
					'app/ux/combo/CVXManufacturersForCvx.js',
					'app/ux/combo/EncounterICDS.js',
					'app/ux/combo/EncounterPriority.js',
					'app/ux/combo/Ethnicity.js',
					'app/ux/combo/Facilities.js',
					'app/ux/combo/FloorPlanAreas.js',
					'app/model/administration/FloorPlanZones.js',
					'app/store/administration/FloorPlanZones.js',
					'app/ux/grid/RowFormEditing.js',
					'app/ux/combo/FloorPlanZones.js',
					'app/ux/combo/FollowUp.js',
					'app/ux/combo/InsurancePayerType.js',
					'app/ux/combo/LabObservations.js',
					'app/ux/combo/LabsTypes.js',
					'app/ux/combo/Languages.js',
					'app/ux/combo/Lists.js',
					'app/ux/combo/MedicalIssues.js',
					'app/ux/combo/Medications.js',
					'app/ux/combo/MsgNoteType.js',
					'app/ux/combo/MsgStatus.js',
					'app/ux/combo/Occurrence.js',
					'app/ux/combo/Outcome.js',
					'app/ux/combo/Outcome2.js',
					'app/ux/combo/PayingEntity.js',
					'app/ux/combo/PaymentMethod.js',
					'app/ux/combo/PaymentCategory.js',
					'app/ux/combo/Pharmacies.js',
					'app/ux/combo/posCodes.js',
					'app/ux/combo/PrescriptionHowTo.js',
					'app/ux/combo/PrescriptionOften.js',
					'app/ux/combo/PrescriptionTypes.js',
					'app/ux/combo/PrescriptionWhen.js',
					'app/ux/combo/PreventiveCareTypes.js',
					'app/ux/combo/ProceduresBodySites.js',
					'app/ux/combo/Providers.js',
					'app/ux/combo/Race.js',
					'app/ux/combo/Roles.js',
					'app/ux/combo/Sex.js',
					'app/ux/combo/Surgery.js',
					'app/ux/combo/SmokingStatus.js',
					'app/ux/combo/TaxId.js',
					'app/ux/combo/Templates.js',
					'app/ux/combo/Themes.js',
					'app/ux/combo/Time.js',
					'app/ux/combo/Titles.js',
					'app/ux/combo/TransmitMethod.js',
					'app/ux/combo/Types.js',
					'app/ux/combo/Units.js',
					'app/ux/combo/Users.js',
					'app/ux/combo/YesNoNa.js',
					'app/ux/combo/YesNo.js',
					'app/ux/window/Window.js',
					'app/view/search/PatientSearch.js',
					'modules/Module.js',
					'app/model/administration/MedicationInstruction.js',
					'app/model/administration/CPT.js',
					'app/ux/LiveRXNORMSearch.js',
					'app/store/administration/CPT.js',
					'app/ux/LiveRadiologySearch.js',
					'app/model/miscellaneous/AddressBook.js',
					'app/model/patient/CarePlanGoal.js',
					'app/model/patient/CognitiveAndFunctionalStatus.js',
					'app/model/patient/SmokeStatus.js',
					'app/model/administration/ActiveProblems.js',
					'app/model/administration/Applications.js',
					'app/model/administration/DefaultDocuments.js',
					'app/model/administration/DocumentsTemplates.js',
					'app/model/administration/DocumentToken.js',
					'app/model/administration/ExternalDataLoads.js',
					'app/model/administration/Facility.js',
					'app/model/administration/FacilityStructure.js',
					'app/model/administration/FloorPlans.js',
					'app/model/administration/FormListOptions.js',
					'app/model/administration/FormsList.js',
					'app/model/administration/Globals.js',
					'app/model/administration/HeadersAndFooters.js',
					'app/model/administration/ImmunizationRelations.js',
					'app/model/administration/InsuranceCompany.js',
					'app/model/administration/LabObservations.js',
					'app/model/administration/Laboratories.js',
					'app/model/administration/LayoutTree.js',
					'app/model/administration/ListOptions.js',
					'app/model/administration/Lists.js',
					'app/model/administration/Modules.js',
					'app/model/administration/Medications.js',
					'app/model/administration/ParentFields.js',
					'app/model/administration/Pharmacies.js',
					'app/model/administration/PreventiveCare.js',
					'app/model/administration/PreventiveCareActiveProblems.js',
					'app/model/administration/PreventiveCareLabs.js',
					'app/model/administration/PreventiveCareMedications.js',
					'app/model/administration/ProviderCredentialization.js',
					'app/model/administration/ReferringProviderFacility.js',
					'app/model/administration/ReferringProvider.js',
					'app/model/administration/Services.js',
					'app/model/administration/Specialty.js',
					'app/model/administration/TemplatePanel.js',
					'app/model/administration/TemplatePanelTemplate.js',
					'app/model/administration/TransactionLog.js',
					'app/model/administration/XtypesComboModel.js',
					'app/model/administration/User.js',
					'app/model/miscellaneous/OfficeNotes.js',
					'app/model/miscellaneous/Amendment.js',
					'app/model/account/VoucherLine.js',
					'app/model/account/Voucher.js',
					'app/model/fees/Billing.js',
					'app/model/fees/Checkout.js',
					'app/model/fees/EncountersPayments.js',
					'app/model/fees/PaymentTransactions.js',
					'app/model/navigation/Navigation.js',
					'app/model/patient/Vitals.js',
					'app/model/patient/FamilyHistory.js',
					'app/model/patient/ReviewOfSystems.js',
					'app/model/patient/HCFAOptions.js',
					'app/model/patient/EncounterService.js',
					'app/model/patient/encounter/snippetTree.js',
					'app/model/patient/encounter/Procedures.js',
					'app/model/patient/AppointmentRequest.js',
					'app/model/patient/AdvanceDirective.js',
					'app/model/patient/Allergies.js',
					'app/model/patient/CheckoutAlertArea.js',
					'app/model/patient/CptCodes.js',
					'app/model/patient/Dental.js',
					'app/model/patient/Disclosures.js',
					'app/model/patient/DismissedAlerts.js',
					'app/model/patient/DoctorsNote.js',
					'app/model/patient/EventHistory.js',
					'app/model/patient/Encounter.js',
					'app/model/patient/CVXCodes.js',
					'app/model/patient/ImmunizationCheck.js',
					'app/model/patient/LaboratoryTypes.js',
					'app/model/patient/MeaningfulUseAlert.js',
					'app/model/patient/Insurance.js',
					'app/model/patient/Medications.js',
					'app/model/patient/Notes.js',
					'app/model/patient/PatientActiveProblem.js',
					'app/model/patient/PatientArrivalLog.js',
					'app/model/patient/PatientCalendarEvents.js',
					'app/model/patient/PatientDocuments.js',
					'app/model/patient/PatientImmunization.js',
					'app/model/patient/PatientLabsResults.js',
					'app/model/patient/PatientsLabOrderItems.js',
					'app/model/patient/PatientSocialHistory.js',
					'app/model/patient/PatientsOrderObservation.js',
					'app/model/patient/PatientsOrderResult.js',
					'app/model/patient/PatientsOrders.js',
					'app/model/patient/PatientsPrescriptionMedications.js',
					'app/model/patient/PatientsPrescriptions.js',
					'app/model/patient/PatientsXrayCtOrders.js',
					'app/model/patient/PreventiveCare.js',
					'app/model/patient/QRCptCodes.js',
					'app/model/patient/Referral.js',
					'app/model/patient/Reminder.js',
					'app/model/patient/Alert.js',
					'app/model/patient/Surgery.js',
					'app/model/patient/VectorGraph.js',
					'app/model/patient/VisitPayment.js',
					'app/model/patient/charts/BMIForAge.js',
					'app/model/patient/charts/HeadCircumferenceInf.js',
					'app/model/patient/charts/LengthForAgeInf.js',
					'app/model/patient/charts/StatureForAge.js',
					'app/model/patient/charts/WeightForAge.js',
					'app/model/patient/charts/WeightForAgeInf.js',
					'app/model/patient/charts/WeightForRecumbentInf.js',
					'app/model/patient/charts/WeightForStature.js',
					'app/model/areas/PatientArea.js',
					'app/model/areas/PoolArea.js',
					'app/model/areas/PoolDropAreas.js',
					'app/view/patient/windows/PreventiveCare.js',
					'app/view/patient/windows/DocumentViewer.js',
					'app/view/patient/windows/NewEncounter.js',
					'app/view/patient/windows/ArrivalLog.js',
					'app/view/patient/windows/DocumentErrorNote.js',
					'app/store/patient/CheckoutAlertArea.js',
					'app/model/patient/Patient.js',
					'app/model/patient/PatientPossibleDuplicate.js',
					'app/view/dashboard/panel/PortalDropZone.js',
					'app/view/dashboard/Dashboard.js',
					'app/view/dashboard/panel/NewResults.js',
					'app/view/dashboard/panel/DailyVisits.js',
					'app/view/areas/FloorPlan.js',
					'app/view/messages/Messages.js',
					'app/view/patient/charts/BPPulseTemp.js',
					'app/view/patient/charts/HeadCircumference.js',
					'app/view/patient/charts/HeightForStature.js',
					'app/store/patient/AppointmentRequests.js',
					'app/view/patient/encounter/AppointmentRequestGrid.js',
					'app/view/patient/encounter/HealthCareFinancingAdministrationOptions.js',
					'app/view/patient/encounter/CurrentProceduralTerminology.js',
					'app/view/patient/encounter/ICDs.js',
					'app/view/patient/windows/PossibleDuplicates.js',
					'app/store/patient/DoctorsNotes.js',
					'app/view/patient/DoctorsNotes.js',
					'app/store/patient/PatientImmunization.js',
					'app/store/patient/Allergies.js',
					'app/store/patient/PatientActiveProblems.js',
					'app/store/patient/Medications.js',
					'app/view/patient/ItemsToReview.js',
					'app/view/patient/EncounterDocumentsGrid.js',
					'app/view/patient/CheckoutAlertsView.js',
					'app/view/patient/Vitals.js',
					'app/view/patient/NewPatient.js',
					'app/view/patient/ProgressNote.js',
					'app/view/patient/Alert.js',
					'app/store/patient/Reminders.js',
					'app/store/patient/Alerts.js',
					'app/view/patient/Reminders.js',
					'app/store/patient/PatientsOrders.js',
					'app/view/patient/Results.js',
					'app/store/patient/PatientSocialHistory.js',
					'app/view/patient/SocialHistory.js',
					'app/view/patient/Visits.js',
					'app/view/patient/VisitCheckout.js',
					'app/view/fees/Billing.js',
					'app/view/fees/PaymentEntryWindow.js',
					'app/view/fees/Payments.js',
					'app/view/administration/practice/Facilities.js',
					'app/model/administration/Department.js',
					'app/store/administration/Departments.js',
					'app/store/administration/Specialties.js',
					'app/store/administration/FacilityStructures.js',
					'app/view/administration/practice/FacilityConfig.js',
					'app/store/administration/InsuranceCompanies.js',
					'app/view/administration/practice/Insurance.js',
					'app/store/administration/Laboratories.js',
					'app/view/administration/practice/Laboratories.js',
					'app/store/administration/Pharmacies.js',
					'app/view/administration/practice/Pharmacies.js',
					'app/model/administration/InsuranceNumber.js',
					'app/store/administration/InsuranceNumbers.js',
					'app/view/administration/practice/ProviderNumbers.js',
					'app/view/administration/practice/Specialties.js',
					'app/view/administration/practice/ReferringProviders.js',
					'app/view/administration/Applications.js',
					'app/view/administration/Globals.js',
					'app/view/administration/Lists.js',
					'app/view/administration/Medications.js',
					'app/view/administration/Modules.js',
					'app/view/administration/FloorPlans.js',
					'app/view/administration/PreventiveCare.js',
					'app/model/administration/AclGroup.js',
					'app/store/administration/AclGroups.js',
					'app/view/administration/practice/Practice.js',
					'app/view/administration/ExternalDataLoads.js',
					'app/view/miscellaneous/Amendments.js',
					'app/view/miscellaneous/MySettings.js',
					'app/view/miscellaneous/OfficeNotes.js',
					'app/view/miscellaneous/Websearch.js',
					'app/view/signature/SignatureWindow.js',
					'app/store/miscellaneous/AddressBook.js',
					'app/store/patient/CarePlanGoals.js',
					'app/store/patient/CognitiveAndFunctionalStatus.js',
					'app/store/patient/SmokeStatus.js',
					'app/store/patient/PatientPossibleDuplicates.js',
					'app/store/administration/ActiveProblems.js',
					'app/store/administration/Applications.js',
					'app/store/administration/DefaultDocuments.js',
					'app/store/administration/DocumentsTemplates.js',
					'app/store/administration/DocumentToken.js',
					'app/store/administration/ExternalDataLoads.js',
					'app/store/administration/Facility.js',
					'app/store/administration/FloorPlans.js',
					'app/store/administration/FormListOptions.js',
					'app/store/administration/FormsList.js',
					'app/store/administration/Globals.js',
					'app/store/administration/HeadersAndFooters.js',
					'app/store/administration/ImmunizationRelations.js',
					'app/store/administration/LabObservations.js',
					'app/store/administration/LayoutTree.js',
					'app/store/administration/ListOptions.js',
					'app/store/administration/Lists.js',
					'app/store/administration/Medications.js',
					'app/store/administration/Modules.js',
					'app/store/administration/ParentFields.js',
					'app/store/administration/PreventiveCare.js',
					'app/store/administration/PreventiveCareActiveProblems.js',
					'app/store/administration/PreventiveCareLabs.js',
					'app/store/administration/PreventiveCareMedications.js',
					'app/store/administration/ProviderCredentializations.js',
					'app/store/administration/Services.js',
					'app/store/administration/TransactionLogs.js',
					'app/store/administration/User.js',
					'app/view/administration/Layout.js',
					'app/store/administration/XtypesComboModel.js',
					'app/store/miscellaneous/OfficeNotes.js',
					'app/store/miscellaneous/Amendments.js',
					'app/store/account/VoucherLine.js',
					'app/store/account/Voucher.js',
					'app/store/fees/Billing.js',
					'app/store/fees/Checkout.js',
					'app/store/fees/EncountersPayments.js',
					'app/store/fees/PaymentTransactions.js',
					'app/store/navigation/Navigation.js',
					'app/store/patient/encounter/snippetTree.js',
					'app/store/patient/encounter/Procedures.js',
					'app/store/patient/AdvanceDirectives.js',
					'app/store/patient/CptCodes.js',
					'app/store/patient/Dental.js',
					'app/store/patient/Disclosures.js',
					'app/store/patient/EncounterServices.js',
					'app/store/patient/Encounters.js',
					'app/store/patient/CVXCodes.js',
					'app/store/patient/ImmunizationCheck.js',
					'app/store/patient/LaboratoryTypes.js',
					'app/store/patient/MeaningfulUseAlert.js',
					'app/store/patient/Notes.js',
					'app/store/patient/Patient.js',
					'app/store/patient/PatientArrivalLog.js',
					'app/store/patient/PatientCalendarEvents.js',
					'app/store/patient/PatientDocuments.js',
					'app/store/patient/DismissedAlerts.js',
					'app/store/patient/PatientLabsResults.js',
					'app/store/patient/PatientsLabOrderItems.js',
					'app/store/patient/PatientsOrderObservations.js',
					'app/store/patient/PatientsOrderResults.js',
					'app/store/patient/PatientsPrescriptionMedications.js',
					'app/store/patient/PatientsPrescriptions.js',
					'app/store/patient/PatientsXrayCtOrders.js',
					'app/store/patient/PreventiveCare.js',
					'app/store/patient/QRCptCodes.js',
					'app/store/patient/Referrals.js',
					'app/store/patient/Surgery.js',
					'app/store/patient/VectorGraph.js',
					'app/store/patient/VisitPayment.js',
					'app/store/patient/Vitals.js',
					'app/store/patient/charts/BMIForAge.js',
					'app/store/patient/charts/HeadCircumferenceInf.js',
					'app/store/patient/charts/LengthForAgeInf.js',
					'app/store/patient/charts/StatureForAge.js',
					'app/store/patient/charts/WeightForAge.js',
					'app/store/patient/charts/WeightForAgeInf.js',
					'app/store/patient/charts/WeightForRecumbentInf.js',
					'app/store/patient/charts/WeightForStature.js',
					'app/store/areas/PatientAreas.js',
					'app/store/areas/PoolAreas.js',
					'app/store/areas/PoolDropAreas.js',
					'app/controller/administration/AuditLog.js',
					'app/controller/administration/CPT.js',
					'app/controller/administration/DataPortability.js',
					'app/controller/administration/FacilityStructure.js',
					'app/controller/administration/HL7.js',
					'app/controller/administration/DecisionSupport.js',
					'app/controller/administration/Documents.js',
					'app/controller/administration/Practice.js',
					'app/controller/administration/ReferringProviders.js',
					'app/controller/administration/Specialties.js',
					'app/controller/administration/TemplatePanels.js',
					'app/controller/administration/Users.js',
					'app/controller/areas/FloorPlan.js',
					'app/controller/dashboard/Dashboard.js',
					'app/controller/dashboard/panel/NewResults.js',
					'app/controller/dashboard/panel/DailyVisits.js',
					'app/controller/miscellaneous/Amendments.js',
					'app/controller/AlwaysOnTop.js',
					'app/controller/Clock.js',
					'app/controller/Cron.js',
					'app/controller/DualScreen.js',
					'app/controller/Header.js',
					'app/controller/InfoButton.js',
					'app/controller/KeyCommands.js',
					'app/controller/LogOut.js',
					'app/controller/Navigation.js',
					'app/controller/ScriptCam.js',
					'app/controller/Support.js',
					'app/controller/Theme.js',
					'app/controller/patient/ActiveProblems.js',
					'app/controller/patient/AdvanceDirectives.js',
					'app/controller/patient/Alerts.js',
					'app/controller/patient/Allergies.js',
					'app/controller/patient/AppointmentRequests.js',
					'app/controller/patient/CarePlanGoals.js',
					'app/controller/patient/CCD.js',
					'app/controller/patient/CCDImport.js',
					'app/controller/patient/CognitiveAndFunctionalStatus.js',
					'app/controller/patient/DecisionSupport.js',
					'app/controller/patient/DoctorsNotes.js',
					'app/controller/patient/FamilyHistory.js',
					'app/controller/patient/HL7.js',
					'app/controller/patient/Immunizations.js',
					'app/controller/patient/Insurance.js',
					'app/controller/patient/ItemsToReview.js',
					'app/controller/patient/Medical.js',
					'app/controller/patient/Medications.js',
					'app/controller/patient/Patient.js',
					'app/controller/patient/ProgressNotesHistory.js',
					'app/controller/patient/RadOrders.js',
					'app/controller/patient/Reminders.js',
					'app/controller/patient/Referrals.js',
					'app/controller/patient/RxOrders.js',
					'app/controller/patient/Social.js',
					'app/controller/patient/Vitals.js',
					'app/controller/patient/Summary.js',
					'app/controller/patient/encounter/EncounterDocuments.js',
					'app/controller/patient/encounter/EncounterSign.js',
					'app/controller/patient/encounter/SuperBill.js',
					'app/controller/patient/encounter/SOAP.js',
					'app/model/patient/EncounterDx.js',
					'app/view/patient/Immunizations.js',
					'app/view/patient/Medications.js',
					'app/view/patient/AdvanceDirectives.js',
					'app/view/patient/LabOrders.js',
					'app/view/patient/SupperBill.js',
					'app/ux/combo/EncounterSupervisors.js',
					'app/view/dashboard/panel/Portlet.js',
					'app/ux/grid/AreasDragDrop.js',
					'app/view/patient/encounter/CarePlanGoals.js',
					'app/ux/LiveSnomedProcedureSearch.js',
					'app/view/patient/encounter/AdministeredMedications.js',
					'app/view/patient/DecisionSupportWarningPanel.js',
					'app/view/patient/Documents.js',
					'app/view/patient/CCD.js',
					'app/view/administration/CPT.js',
					'app/ux/grid/Button.js',
					'app/ux/combo/XCombo.js',
					'app/ux/form/fields/plugin/PasswordStrength.js',
					'app/ux/combo/ActiveSpecialties.js',
					'app/ux/AddTabButton.js',
					'app/model/administration/AclGroupPerm.js',
					'app/view/patient/windows/ArchiveDocument.js',
					'app/view/scanner/Window.js',
					'app/view/notifications/Grid.js',
					'app/view/patient/windows/UploadDocument.js',
					'app/view/administration/HL7MessageViewer.js',
					'app/view/patient/encounter/Snippets.js',
					'app/ux/combo/ReferringProviders.js',
					'app/ux/LiveSnomedProblemSearch.js',
					'app/view/patient/SmokingStatus.js',
					'app/ux/LiveSnomedSearch.js',
					'app/ux/LiveRadsSearch.js',
					'app/store/administration/MedicationInstructions.js',
					'app/model/patient/SOAP.js',
					'app/view/patient/windows/Charts.js',
					'app/view/patient/windows/EncounterCheckOut.js',
					'app/view/dashboard/panel/PortalColumn.js',
					'app/view/dashboard/panel/PortalPanel.js',
					'app/view/areas/PatientPoolAreas.js',
					'app/view/administration/DataManager.js',
					'app/view/administration/Documents.js',
					'app/view/administration/Roles.js',
					'app/view/administration/Users.js',
					'app/view/miscellaneous/AddressBook.js',
					'app/view/miscellaneous/MyAccount.js',
					'app/view/patient/Patient.js',
					'app/view/patient/Summary.js',
					'app/controller/administration/Roles.js',
					'app/controller/DocumentViewer.js',
					'app/controller/Scanner.js',
					'app/controller/Notification.js',
					'app/controller/patient/Documents.js',
					'app/controller/patient/LabOrders.js',
					'app/controller/patient/Results.js',
					'app/controller/patient/encounter/Encounter.js',
					'app/controller/patient/encounter/Snippets.js',
					'app/view/patient/Referrals.js',
					'app/view/patient/ActiveProblems.js',
					'app/view/patient/SocialPanel.js',
					'app/view/patient/CognitiveAndFunctionalStatus.js',
					'app/view/patient/RadOrders.js',
					'app/view/patient/encounter/CarePlanGoalsNewWindow.js',
					'app/ux/combo/MedicationInstructions.js',
					'app/ux/form/fields/plugin/HelpIcon.js',
					'app/ux/combo/ComboResettable.js',
					'app/ux/form/SearchField.js',
					'app/model/administration/Allergies.js',
					'app/ux/form/fields/CheckBoxWithText.js',
					'app/view/patient/RxOrders.js',
					'app/ux/combo/Specialties.js',
					'app/view/patient/encounter/SOAP.js',
					'app/view/patient/encounter/ProgressNotesHistory.js',
					'app/ux/LiveAllergiesSearch.js',
					'app/view/patient/Allergies.js',
					'app/view/patient/windows/Medical.js',
					'app/ux/form/fields/CheckBoxWithFamilyRelation.js',
					'app/view/patient/encounter/FamilyHistory.js',
					'app/view/patient/Encounter.js',
					'app/view/Viewport.js',
					'app/ux/form/fields/UploadBase64.js',
					'app/ux/form/fields/BoxSelect.js',
					'app/view/patient/windows/CCDImportPreview.js',
					'app/view/patient/windows/CCDImport.js',
					'app/ux/combo/Departments.js',
					'app/model/patient/ProgressNotesHistory.js',
					'app/store/patient/ProgressNotesHistory.js',
					'app/store/administration/TemplatePanels.js',
					'app/view/patient/windows/TemplatePanels.js'

				],
				dest: 'app/app.js'
			}
		},
		uglify: {
			my_target: {
				files: {
					'app/app.min.js': ['app/app.js']
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('default', ['concat', 'uglify']);

};
