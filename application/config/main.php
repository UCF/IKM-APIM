<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Main config variables for app
*

*
*/

$config['all'] = array('Admin','All');


/** tables - make them portable incase main tables change**/
$config['tables']['plans_extended']           = 'APIM_Plans_Extended';
$config['tables']['subplans_extended']        = 'APIM_Sub_Plans_Extended';
$config['tables']['strategic_emphasis']       = 'APIM_Strategic_Emphasis';
$config['tables']['tracking']			      = 'APIM_Tracking';
$config['tables']['regional_locations']		  = 'APIM_Regional_Locations';
$config['tables']['plan_regional_link']		  = 'APIM_Plan_Regional_Link';
$config['tables']['subplan_regional_link']	  = 'APIM_Plan_SubPlan_Regional_Link';


/** Tool Tips **/
$config['Tips'] = array(
		
		'College' => 'Short college name',
		'CIP' => 'The Classification of Instructional Program (CIP) code related to the plan and sub-plan',
		'Term Start' => 'Term/Semester when the plan or sub-plan was implemented',
		'Plan Name' => 'Official name of the plan or sub-plan',
		'Strat. Emphasis' => 'Is the plan or sub-plan identified as one of strategic emphasis as defined by the Florida Board of Governors (FLBOG).',
		'Regional' => 'Is the plan or sub-plan offered at a regional campus.',
		'Professional' => 'Degrees that are not considered research focused, but instead prepare students for careers outside of academia.',
		'Plan' => 'Official PeopleSoft code for the academic plan.',
		'Sub-plan' => 'Official PeopleSoft code for the academic sub-plan.',
		'Plan Type' => 'Is the plan or sub-plan a track (TRK) or specialization(SPC).',
		'Degree' => 'Type of degree asociated with the plan e.g. Pending (PND), Certificate (CER or CRT), Bachelors (BS), Master\'s (MS).',
		'Career' => 'Acadmic career of the plan or sub-plan, Undergraduate (UGRAD), Graduate (GRAD)',
		'Status' => 'Status of the plan or sub-plan, Active(A), Inactive(I), Suspended(S)',
		'Susp./Inact. Date' => 'Term/semester when the plan or sub-plan became inactive or suspended if applicable.',
		'Access' => 'Is the plan or sub-plan restricted or unrestricted enrollment.',
		'Admission' => '???',
		'Re-Admit' => '???',
		'FLVC Transient' => 'Is the plan or sub-plan listed with the Florida Virtual Consortium (FLVC).',
		'UCF Online' => 'Is the plan or sub-plan an Online program.',		
		'STEM' => 'Is the plan or sub-plan identified as a STEM area (Science, Technology, Engineering, and Math Education).',		
		'Plan Name Extra' => 'Graduate studies naming convention for plan',
		'SubPlan Name Extra' => 'Graduate studies naming convention for Sub-Plans',
		'Tot.Thesis Hrs' => 'Total thesis hours required for Plan or Sub-Plan',
		'Professional' => 'Is the plan or sub-plan part of the Professional Science Master\'s program'		
		
);