<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Main config variables for app
*

*
*/

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


/** Tool Tips **/
$config['Tips'] = array(
		
		'College' => 'Short college name where plan is located',
		'Dept.' => 'Department where plan is located',
		'CIP' => 'The Classification of Instructional Program (CIP) code related to the plan and sub-plan',
		'Term Start' => 'Term/Semester when the plan or sub-plan was implemented',
		'Plan Name' => 'Official name of the plan or sub-plan',
		'Strat. Emphasis' => 'Is the plan or sub-plan identified as one of strategic emphasis as defined by the Florida Board of Governors (FLBOG).',
		'Regional?' => 'Is the plan or sub-plan offered at a regional campus.',
		'Program' => 'Official PeopleSoft program code for plan or sub-plan.',
		'Professional' => 'Degrees that are not considered research focused, but instead prepare students for careers outside of academia.',
		'Plan' => 'Official PeopleSoft plan code.',
		'Sub-plan' => 'Official PeopleSoft sub-plan code (where applicable).',
		'Plan' => 'Official PeopleSoft code for the academic plan.',
		'Sub-plan' => 'Official PeopleSoft code for the academic sub-plan.',
		'Plan Type' => 'Is the plan or sub-plan a track (TRK) or specialization(SPC).',
		'Degree' => 'Type of degree asociated with the plan e.g. Pending (PND), Certificate (CER or CRT), Bachelors (BS), Master\'s (MS).',
		'Career' => 'Acadmic career of the plan or sub-plan, Undergraduate (UGRAD), Graduate (GRAD)',
		'Level' => 'Level of plan e.g. Bachelors',
		'Status' => 'Status of the plan or sub-plan, Active(A), Inactive(I), Suspended(S)',
		'Susp./Inact. Date' => 'Term/semester when the plan or sub-plan became inactive or suspended if applicable.',
		'Access' => 'Is the plan or sub-plan restricted or unrestricted enrollment.',
		'Admission' => 'Should the plan or sub-plan be loaded on the requisite admissions form (Grad or Undergrad).',
		'Re-Admit' => 'Should the plan or sub-plan be loaded on the Readmission form.',
		'Load As' => 'Use a different plan from CIP group allowing students to admit too.',
		'FLVC Transient' => 'Is the plan or sub-plan listed with the Florida Virtual Consortium (FLVC).',
		'UCF Online' => 'Is the plan or sub-plan a UCF Online program.',		
		'STEM' => 'Is the plan or sub-plan identified as a STEM area (Science, Technology, Engineering, and Math Education).',		
		'Program/Plan Extra' => 'Additional name for the plan',
		'SubPlan Name Extra' => 'Additional name for the sub-plan',
		'Tot. Thesis Hrs.' => 'Total thesis hours required for Plan or Sub-Plan',
		'Professional' => 'Is the plan or sub-plan part of the Professional Science Master\'s program',
		'Mrkt. Rate Tuition' => 'Is the plan or sub-plan defined as market rate.',
		'Cost Recovery' => 'Is the plan or sub-plan a cost recovery program.',
		'Dept. Name Extra' => 'Additional name for the Department',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for plan or sub-plan',
		'Tot. Non-Thesis Hrs.' => 'Total non-thesis hours required for plan or sub-plan',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for Plan or sub-plan',
		'Tot. Cert. Hrs.' => 'Total certificate hours for the certificate plan or sub-plan.',
		'Tot. Dissertation Hrs.' => 'Total dissertations hours required for Plan or Sub-Plan',
		'Tot. Doctoral Hrs.' => 'Total doctoral hours required for Plan or Sub-Plan',
		'Lake Nona' => "Is the plan or sub-plan offered at the Lake Nona Campus?",
		'Rosen' => "Is the plan or sub-plan offered at the Rosen Campus?",
		'Main Campus' => "Is the plan or sub-plan offered at the Main Campus?",
		'Altamonte Springs' => "Is the plan or sub-plan offered at the Altamonte Springs Campus?",
		'Cocoa' => "Is the plan or sub-plan offered at the Cocoa Beach Campus?",
		'Daytona' => "Is the plan or sub-plan offered at the Daytona Beach Campus?",
		'Leesburg' => "Is the plan or sub-plan offered at the Leesburg Campus?",
		'Ocala' => "Is the plan or sub-plan offered at the Ocala Campus?",
		'Palm Bay' => "Is the plan or sub-plan offered at the Palm Bay Campus?",
		'Sanford/LM' => "Is the plan or sub-plan offered at the Sanford - Lake Mary Campus?",
		'South Lake' => "Is the plan or sub-plan offered at the South Lake Campus?",
		'Valencia East' => "Is the plan or sub-plan offered at the Valencia East Campus?",
		'Valencia Osceola' => "Is the plan or sub-plan offered at the Valencia Osceola Campus?",
		'Valencia West' => "Is the plan or sub-plan offered at the Valencia West Campus?"
		
);