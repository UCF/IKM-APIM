<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Main config variables for app
*

*
*/
$config['openclose'] = 1; /** IS Apim open or closed to colleges 1 for open; 0 for closed **/
$config['restrict'] = array('CAH','CBA','COS','COHPA','CON','EDUC','ENGR','COM','HSPMG','OPTIC','UGST','UGRD');

$config['all'] = array('Admin','All');
$config['admin'] = array('Admin');


/** tables - make them portable incase main tables change**/
$config['tables']['plans_extended']           = 'APIM_Plans_Extended';
$config['tables']['subplans_extended']        = 'APIM_Sub_Plans_Extended';
$config['tables']['strategic_emphasis']       = 'APIM_Strategic_Emphasis';
$config['tables']['tracking']			      = 'APIM_Tracking';
$config['tables']['regional_locations']		  = 'APIM_Regional_Locations';
$config['tables']['plan_regional_link']		  = 'APIM_Plan_Regional_Link';
$config['tables']['subplan_regional_link']	  = 'APIM_Plan_SubPlan_Regional_Link';
$config['tables']['stem_cip']				  = 'APIM_STEM_CIPS';
$config['tables']['users']				 	  = 'APIM_users';
$config['tables']['groups']				  = 'APIM_groups';
$config['tables']['usergroups']				  = 'APIM_users_groups';


/** Tool Tips **/
$config['Tips'] = array(
		
		'College' => 'Short college name where Plan is located',
		'Dept.' => 'Department where Plan is located',
		'CIP' => 'The Classification of Instructional Program (CIP) code related to the Plan and SubPlan',
		'Term Start' => 'Term/Semester when the Plan or SubPlan was implemented',
		'Plan Name' => 'Official name of the Plan or SubPlan',
		'Strat. Emphasis' => 'Is the Plan or SubPlan identified as one of strategic emphasis as defined by the Florida Board of Governors (FLBOG).',
		'Regional?' => 'Is the Plan or SubPlan offered at a regional campus.',
		'Program' => 'Official PeopleSoft program code for Plan or SubPlan.',
		'Professional' => 'Degrees that are not considered research focused, but instead prepare students for careers outside of academia.',
		'Plan' => 'Official PeopleSoft Plan code.',
		'SubPlan' => 'Official PeopleSoft SubPlan code (where applicable).',
		'AS:BS Articulated' => 'Articulated AS to BS',
		'Plan' => 'Official PeopleSoft code for the academic Plan.',
		'SubPlan' => 'Official PeopleSoft code for the academic SubPlan.',
		'Plan Type' => 'Is the Plan or SubPlan a track (TRK) or specialization(SPC).',
		'Degree' => 'Type of degree asociated with the Plan e.g. Pending (PND), Certificate (CER or CRT), Bachelors (BS), Master\'s (MS).',
		'Career' => 'Acadmic career of the Plan or SubPlan, Undergraduate (UGRAD), Graduate (GRAD)',
		'Level' => 'Level of Plan e.g. Bachelors',
		'Status' => 'Status of the Plan or SubPlan, Active(A), Inactive(I), Suspended(S)',
		'Susp./Inact. Date' => 'Term/semester when the Plan or SubPlan became inactive or suspended if applicable.',
		'Access' => 'Is the Plan or SubPlan restricted or unrestricted enrollment.',
		'Admission' => 'Should the Plan or SubPlan be loaded on the requisite admissions form (Grad or Undergrad).',
		'Readmit' => 'Should the Plan or SubPlan be loaded on the Readmission form.',
		'Load As' => 'Use a different Plan from CIP group allow students to admit too.',
		'FLVC Transient' => 'Is the Plan or SubPlan listed with the Florida Virtual Campus (FLVC).',
		'UCF Online' => 'Is the Plan or SubPlan a UCF Online program.',		
		'UCF STEM' => 'Is the Plan or SubPlan identified as a STEM area (Science, Technology, Engineering, and Math Education).',		
		'Program/Plan Extra' => 'Additional name for the Plan',
		'SubPlan Name Extra' => 'Additional name for the SubPlan',
		'Tot. Thesis Hrs.' => 'Total thesis hours required for Plan or SubPlan',
		'Professional' => 'Is the Plan or SubPlan part of the Professional Science Master\'s program',
		'Mrkt. Rate Tuition' => 'Is the Plan or SubPlan defined as market rate.',
		'Cost Recovery' => 'Is the Plan or SubPlan a cost recovery program.',
		'Dept. Name Extra' => 'Additional name for the Department',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for Plan or SubPlan',
		'Tot. Non-Thesis Hrs.' => 'Total non-thesis hours required for Plan or SubPlan',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for Plan or SubPlan',
		'Tot. Cert. Hrs.' => 'Total certificate hours for the certificate Plan or SubPlan.',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for Plan or SubPlan',
		'Tot. Doctoral Hrs.' => 'Total doctoral hours required for Plan or SubPlan',
		'Lake Nona' => "Is the Plan or SubPlan offered at the Lake Nona Campus?",
		'Rosen' => "Is the Plan or SubPlan offered at the Rosen Campus?",
		'Orlando' => "Is the Plan or SubPlan offered at the OrlandoCampus?",
		'Altamonte Springs' => "Is the Plan or SubPlan offered at the Altamonte Springs Campus?",
		'Cocoa' => "Is the Plan or SubPlan offered at the Cocoa Beach Campus?",
		'Daytona' => "Is the Plan or SubPlan offered at the Daytona Beach Campus?",
		'Leesburg' => "Is the Plan or SubPlan offered at the Leesburg Campus?",
		'Ocala' => "Is the Plan or SubPlan offered at the Ocala Campus?",
		'Palm Bay' => "Is the Plan or SubPlan offered at the Palm Bay Campus?",
		'Sanford/LM' => "Is the Plan or SubPlan offered at the Sanford - Lake Mary Campus?",
		'South Lake' => "Is the Plan or SubPlan offered at the South Lake Campus?",
		'Valencia East' => "Is the Plan or SubPlan offered at the Valencia East Campus?",
		'Valencia Osceola' => "Is the Plan or SubPlan offered at the Valencia Osceola Campus?",
		'Valencia West' => "Is the Plan or SubPlan offered at the Valencia West Campus?"
		
);