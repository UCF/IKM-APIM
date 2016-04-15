<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V1 extends CI_Controller {
	
function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
		header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token'); // allow certain headers
		header("Cache-Control: no-store, no-cache, must-revalidate");
        	header("Cache-Control: post-check=0, pre-check=0", false);
        	header("Pragma: no-cache");
        	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		parent::__construct();
		
		$this->load->model('General_model');
		$this->load->library(array('ion_auth','form_validation','corelib'));
		$this->load->helper(array('url','language'));

		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		$this->lang->load('auth');
		$this->load->config('main', TRUE);
	}

	public function test()
		{
			$final_data = '';
		
			//get user information
			$user = $this->ion_auth->user()->row();
			
			
			$college = $this->input->get('college');		
			if($college == '') { $college = '%'; }
			
			$career = $this->input->get('career');
			if($career == '') { $career = '%'; }
					
			$plan_data = $this->General_model->acadplan_all('ASC','Acad_Plan',$college,$career);	
					
			$itemlist = array();
			$id = 0;
					
						
			foreach ($plan_data->result() as $key => $row){
				$id++;			
				$access = '';
				$status_change = '';
				$avail = true;
				$regional = 'No';
				$subplan = 'No';
				$sub_plans = '';
				$campuses = array();
				$region_item = array();
				$meta_data = array();
				$meta = array();
				
				//set the regional data vars
				$altamonte = 0;
				$cocoa= 0;
				$daytona = 0;
				$leesburg = 0;
				$melbourne = 0;
				$ocala = 0;
				$palmbay = 0;
				$sanford = 0;
				$southlake = 0;
				$valenciaeast = 0;
				$valenciaosce = 0;
				$valenciawest = 0;
					
				
				//get the access information
				$access_data = $this->General_model->get_access($row->Acad_Plan);
				if($access_data->num_rows()){
					//return one row
					$arow = $access_data->row();			
					$access = $arow->Prg_Access;
				} 
				
				//set status change date for Inactives and Suspends only
				if($row->Status == 'I' || $row->Status == 'S'){ 
					$status_change = ucfirst(strtolower($row->Cancelled_Year)); 
				}
				
				//check for and set Regional flag with the custom system library
				$plan_regions = $this->corelib->plan_locations($row->Acad_Plan);
				if($plan_regions){
					$regional = 'Yes'; //set the identifier for regional
					foreach ($plan_regions->result() as $pl_key => $pl_row){
						$locale = trim($pl_row->Location_Code);
							
						switch($locale){
							case 'ALTSPRNG':
								$altamonte = 1;
								break;
							case "COCOA":
								$cocoa = 1;
								break;
							case "DAYTONA":
								$daytona = 1;
								break;
							case "LEESBURG":
								$leesburg = 1;
								break;
							case "MELBOURNE":
								$melbourne = 1;
								break;
							case "OCALA":
								$ocala = 1;
								break;
							case "PALM BAY":
								$palmbay = 1;
								break;
							case "LAKE MARY":
								$sanford = 1;
								break;
							case "SOUTH LAKE":
								$southlake = 1;
								break;
							case "OSCEOLA":
								$valenciaosce = 1;
								break;
							case "METROWEST":
								$valenciawest = 1;
								break;
							case "VALENCIA":
								$valenciaeast = 1;
								break;
						}
					}
					
				}
			
				
				//get the plan extras; set zeros if no record exists
				$plan_extra = $this->General_model->get_plan_extra($row->Acad_Plan);
				if(!$plan_extra->num_rows()){
					$admission = 0;
					$readmit = 0;
					$flvc = 0;
					$orient = 0;
					$online = 0;
					$psm = 0;
					$stem = 0;
					$mtr = 0;
					$professional = 0;
					$totThesis = 0;
					$totNonThesis = 0;
					$tot6971 = 0;
					$totCert = 0;
					$totDoc = 0;
					$totDissert = 0;				
					$recent = '';
					$plan_long_name = '';
				} else {
					$plan_extra_row = $plan_extra->row();
					$plan_long_name = $plan_extra_row->Long_Name;
					$admission = $plan_extra_row->Admission;
					$readmit = $plan_extra_row->Readmit;
					$flvc = $plan_extra_row->FLVC;
					$orient = $plan_extra_row->Orientation;
					$online = $plan_extra_row->Online;
					$psm = $plan_extra_row->psm;
					$stem = $plan_extra_row->STEM;	
					$professional = $plan_extra_row->professional;				
					$mtr = $plan_extra_row->MTR;
					$totThesis = $plan_extra_row->Total_Thesis;
					$totNonThesis = $plan_extra_row->Total_NonThesis;
					$tot6971 = $plan_extra_row->Total_Thesis6971;
					$totDoc = $plan_extra_row->Total_Doctoral;
					$totDissert = $plan_extra_row->Total_Dissertation;
					$tot6971 = $plan_extra_row->Total_Thesis6971;
					$totCert = $plan_extra_row->Total_Grad_Certificate;			
	
					$recent = $plan_extra_row->Recent_Change;
					//$timestamp = strtotime($plan_extra_row->Recent_Change);
					//$recent = date('m/d/yyy', $timestamp);
				
				}
				
				//subplan fun
				$sub_data = $this->General_model->subplan_all($row->Acad_Plan);
				if($sub_data->num_rows()){
					$subplan = "Yes";
						
					$sub_plans = $this->subplan($sub_data,$row->Acad_Plan);
				}
					
				//put the regionals in  own sublevels
				$region_item = array(
						"ALTSPRNG" => $altamonte,
						"COCOA" => $cocoa,
						"DAYTONA" => $daytona,
						"LEESBURG" => $leesburg,
						"MELBOURNE" => $melbourne,
						"OCALA" => $ocala,
						"PALMBAY" => $palmbay,
						"LAKEMARY" => $sanford,
						"SOUTHLAKE" => $southlake,
						"OSCEOLA" => $valenciaosce,
						"METROWEST" => $valenciawest,
						"VALENCIA" => $valenciaeast
				);
				$campuses[] = $region_item;
				
				$meta_data = array(
						"TermStart"=> ucfirst(strtolower($row->Term)),
						"Regional"=> $regional,
						"SubPlan"=> $subplan,
						"PlanLongName"=> $plan_long_name,
						"Stratemph"=> $row->AREA,						
						"College" => $row->College,
						"Career" => $row->Career,
						"Plan Type" => '',
						"Degree" => $row->Degree,
						"Dept." => $row->AcadOrgDescr,
						"Status" => $row->Status,
						"StatusChangeTerm" => $status_change,
						"Access" => $access,
						"Admission" => $admission,
						"ReAdmit" => $readmit,
						"FLVC" => $flvc,
						"Online" => $online,
						"Orientation" => $orient,
						"PSM" => $psm,
						"STEM" => $stem,
						"Professional" => $professional,
						"MTR" => $mtr,
						"CR"=> 0,
						"TotThesis" => $totThesis,
						"Tot6971" => $tot6971,
						"TotNonThesis" => $totNonThesis,
						"TotCert" => $totCert,
						"TotDoc" => $totDoc,
						"TotDissert" => $totDissert
				);
				$meta[] = $meta_data;
				
				$item = array(	
						"id" => $id,
						"Plan"=> $row->Acad_Plan,
						"PlanName"=> $row->UCF_Name,
						"CIP" => $row->CIP_Code,
						"HEGIS" => $row->HEGIS_Code,
	                    "Meta Data"=> $meta,
						"Regional Campuses" => $campuses,
						"SubPlans" => $sub_plans
					);
				$itemlist[] = $item;
				
				
				//get the Subs associated with the plans
				/*$sub_plan_data = $this->General_model->subplan_all($row->Acad_Plan);
				
				if($sub_plan_data->num_rows()){			
					foreach($sub_plan_data->result() as $sub_key => $sub_row){
						$id++;
						$sub_regional = '';
						$adm = array('true','false');
						$admk = array_rand($adm);
						
						//set the regional data vars
						$altamonte = 0;
						$cocoa= 0;
						$daytona = 0;
						$leesburg = 0;
						$melbourne = 0;
						$ocala = 0;
						$palmbay = 0;
						$sanford = 0;
						$southlake = 0;
						$valenciaeast = 0;
						$valenciaosce = 0;
						$valenciawest = 0;
							
						//check for and set Regional flag with the custom system library
						$subplan_regions = $this->corelib->subplan_locations($row->Acad_Plan,$sub_row->Sub_Plan);
						if($subplan_regions){
							$sub_regional = 'Yes';
							foreach ($subplan_regions->result() as $spl_key => $spl_row){
								$sublocale = trim($spl_row->Location_Code);
						
								switch($sublocale){
									case 'ALTSPRNG':
										$altamonte = 1;
										break;
									case "COCOA":
										$cocoa = 1;
										break;
									case "DAYTONA":
										$daytona = 1;
										break;
									case "LEESBURG":
										$leesburg = 1;
										break;
									case "MELBOURNE":
										$melbourne = 1;
										break;
									case "OCALA":
										$ocala = 1;
										break;
									case "PALM BAY":
										$palmbay = 1;
										break;
									case "LAKE MARY":
										$sanford = 1;
										break;
									case "SOUTH LAKE":
										$southlake = 1;
										break;
									case "OSCEOLA":
										$valenciaosce = 1;
										break;
									case "METROWEST":
										$valenciawest = 1;
										break;
									case "VALENCIA":
										$valenciaeast = 1;
										break;
								}
							}
						
						}
						//get the plan extras; set zeros if no record exists
						$subplan_extra = $this->General_model->get_sub_plan_extra($row->Acad_Plan,$sub_row->Sub_Plan);
						
						if(!$subplan_extra->num_rows()){
							$admission = 0;
							$readmit = 0;
							$flvc = 0;
							$orient = 0;
							$online = 0;
							$psm = 0;
							$stem = 0;
							$mtr = 0;
							$professional = 0;
							$totThesis = 0;
							$totNonThesis = 0;
							$totCert = 0;
							$totDoc = 0;
							$tot6971 = 0;
							$totDissert = 0;
							$sub_long_name = '';
						} else {
							$subplan_extra_row = $subplan_extra->row();
							$sub_long_name = $subplan_extra_row->Long_Name;
							$admission = $subplan_extra_row->Admission;
							$readmit = $subplan_extra_row->Readmit;
							$flvc = $subplan_extra_row->FLVC;
							$online = $subplan_extra_row->Online;
							$orient = $subplan_extra_row->Orientation;
							$psm = $subplan_extra_row->psm;
							$stem = $subplan_extra_row->STEM;	
							$professional = $subplan_extra_row->professional;				
							$mtr = $subplan_extra_row->MTR;
							$totThesis = $subplan_extra_row->Total_Thesis;
							$totNonThesis = $subplan_extra_row->Total_NonThesis;
							$tot6971 = $subplan_extra_row->Total_Thesis6971;
							$totCert = $subplan_extra_row->Total_Grad_Certificate;
							$totDoc = $subplan_extra_row->Total_Doctoral;
							$totDissert = $subplan_extra_row->Total_Dissertation;
						}				
						
						$sub_item = array("id" => $id,
								  "Plan"=> $sub_row->Acad_Plan,
								  "PlanLongName"=> $plan_long_name,
								  "SubPlanLongName"=>$sub_long_name,
								  "TermStart"=> ucfirst(strtolower($sub_row->Term)),
								  "Regional"=>$sub_regional,
								  "Stratemph"=> $row->AREA,
								  "Subplan"=> $sub_row->Sub_Plan,
								  "Plan Name"=> $sub_row->UCF_Name,
								  "College" => $row->College,
								  "Career" => $row->Career,
								  "CIP" => $row->CIP_Code,
								  "HEGIS" => $sub_row->HEGIS_Code,
								  "Status" => $sub_row->Status,
								  "Access" => $access,
								  "Plan Type" => $sub_row->Sub_Pl_Typ,
								  "Degree" => $row->Degree,
								  "Dept." => $row->AcadOrgDescr,
								  "Admission" => $admission,
								  "ReAdmit" => $readmit,
								  "FLVC" => $flvc,
								  "Orientation" => $orient,
								  "Online" => $online,
								  "PSM" => $psm,
								  "STEM" => $stem,
								  "Professional" => $professional,
								  "MTR" => $mtr,
									"CR"=> 0,
								  "TotThesis" => $totThesis,
								  "TotNonThesis" => $totNonThesis,
								  "Tot6971" => $tot6971,
								  "TotCert" => $totCert,
								  "TotDoc" => $totDoc,
								  "TotDissert" => $totDissert,
									"ALTSPRNG" => $altamonte,
									"COCOA" => $cocoa,
									"DAYTONA" => $daytona,
									"LEESBURG" => $leesburg,
									"MELBOURNE" => $melbourne,
									"OCALA" => $ocala,
									"PALMBAY" => $palmbay,
									"LAKEMARY" => $sanford,
									"SOUTHLAKE" => $southlake,
									"OSCEOLA" => $valenciaosce,
									"METROWEST" => $valenciawest,
									"VALENCIA" => $valenciaeast,
								  "avail" => $avail,
								  "info" => 'T'
							);
						$itemlist[] = $sub_item;
				
					}
				}*/
			}
			
			$result_count = count($itemlist);
			
			//fix utf-8 issue with MSSQL
			$fixed = $this->corelib->utf8_converter($itemlist);
	
			//convert to json and escape any weird chracters
			$final_data = json_encode($fixed, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		
			header('Content-Type: text/plain');
			echo  $final_data;
		}
	public function subplan($sub_plan_data,$acad_plan){
		foreach($sub_plan_data->result() as $sub_key => $sub_row){
			
			$sub_regional = '';
			$adm = array('true','false');
			$admk = array_rand($adm);
			$meta = array();
			$meta_data = array();
			$campuses = array();
		
			//set the regional data vars
			$altamonte = 0;
			$cocoa= 0;
			$daytona = 0;
			$leesburg = 0;
			$melbourne = 0;
			$ocala = 0;
			$palmbay = 0;
			$sanford = 0;
			$southlake = 0;
			$valenciaeast = 0;
			$valenciaosce = 0;
			$valenciawest = 0;
				
			//check for and set Regional flag with the custom system library
			$subplan_regions = $this->corelib->subplan_locations($acad_plan,$sub_row->Sub_Plan);
			if($subplan_regions){
				$sub_regional = 'Yes';
				foreach ($subplan_regions->result() as $spl_key => $spl_row){
					$sublocale = trim($spl_row->Location_Code);
		
					switch($sublocale){
						case 'ALTSPRNG':
							$altamonte = 1;
							break;
						case "COCOA":
							$cocoa = 1;
							break;
						case "DAYTONA":
							$daytona = 1;
							break;
						case "LEESBURG":
							$leesburg = 1;
							break;
						case "MELBOURNE":
							$melbourne = 1;
							break;
						case "OCALA":
							$ocala = 1;
							break;
						case "PALM BAY":
							$palmbay = 1;
							break;
						case "LAKE MARY":
							$sanford = 1;
							break;
						case "SOUTH LAKE":
							$southlake = 1;
							break;
						case "OSCEOLA":
							$valenciaosce = 1;
							break;
						case "METROWEST":
							$valenciawest = 1;
							break;
						case "VALENCIA":
							$valenciaeast = 1;
							break;
					}
				}
		
			}
			
			//get the plan extras; set zeros if no record exists
			$subplan_extra = $this->General_model->get_sub_plan_extra($acad_plan,$sub_row->Sub_Plan);
		
			if(!$subplan_extra->num_rows()){
				$admission = 0;
				$readmit = 0;
				$flvc = 0;
				$orient = 0;
				$online = 0;
				$psm = 0;
				$stem = 0;
				$mtr = 0;
				$professional = 0;
				$totThesis = 0;
				$totNonThesis = 0;
				$totCert = 0;
				$totDoc = 0;
				$tot6971 = 0;
				$totDissert = 0;
				$sub_long_name = '';
			} else {
				$subplan_extra_row = $subplan_extra->row();
				$sub_long_name = $subplan_extra_row->Long_Name;
				$admission = $subplan_extra_row->Admission;
				$readmit = $subplan_extra_row->Readmit;
				$flvc = $subplan_extra_row->FLVC;
				$online = $subplan_extra_row->Online;
				$orient = $subplan_extra_row->Orientation;
				$psm = $subplan_extra_row->psm;
				$stem = $subplan_extra_row->STEM;
				$professional = $subplan_extra_row->professional;
				$mtr = $subplan_extra_row->MTR;
				$totThesis = $subplan_extra_row->Total_Thesis;
				$totNonThesis = $subplan_extra_row->Total_NonThesis;
				$tot6971 = $subplan_extra_row->Total_Thesis6971;
				$totCert = $subplan_extra_row->Total_Grad_Certificate;
				$totDoc = $subplan_extra_row->Total_Doctoral;
				$totDissert = $subplan_extra_row->Total_Dissertation;
			}
			
			//put the regionals in  own sublevels
			$region_item = array(
					"ALTSPRNG" => $altamonte,
					"COCOA" => $cocoa,
					"DAYTONA" => $daytona,
					"LEESBURG" => $leesburg,
					"MELBOURNE" => $melbourne,
					"OCALA" => $ocala,
					"PALMBAY" => $palmbay,
					"LAKEMARY" => $sanford,
					"SOUTHLAKE" => $southlake,
					"OSCEOLA" => $valenciaosce,
					"METROWEST" => $valenciawest,
					"VALENCIA" => $valenciaeast
			);
			$campuses[] = $region_item;
			
			$meta_data = array(
			
					"SubPlanLongName"=>$sub_long_name,
					"TermStart"=> ucfirst(strtolower($sub_row->Term)),
					"Regional"=>$sub_regional,					
					"Status" => $sub_row->Status,
					"Plan Type" => $sub_row->Sub_Pl_Typ,
					"Admission" => $admission,
					"ReAdmit" => $readmit,
					"FLVC" => $flvc,
					"Orientation" => $orient,
					"Online" => $online,
					"PSM" => $psm,
					"STEM" => $stem,
					"Professional" => $professional,
					"MTR" => $mtr,
					"CR"=> 0,
					"TotThesis" => $totThesis,
					"TotNonThesis" => $totNonThesis,
					"Tot6971" => $tot6971,
					"TotCert" => $totCert,
					"TotDoc" => $totDoc,
					"TotDissert" => $totDissert,
					"ALTSPRNG" => $altamonte,
					"COCOA" => $cocoa,
					"DAYTONA" => $daytona,
					"LEESBURG" => $leesburg,
					"MELBOURNE" => $melbourne,
					"OCALA" => $ocala,
					"PALMBAY" => $palmbay,
					"LAKEMARY" => $sanford,
					"SOUTHLAKE" => $southlake,
					"OSCEOLA" => $valenciaosce,
					"METROWEST" => $valenciawest,
					"VALENCIA" => $valenciaeast
		
			);
			$meta[] = $meta_data;
			
			$sub_item = array(
				"Subplan"=> $sub_row->Sub_Plan,
				"Subplan_Name" => $sub_row->UCF_Name,
				"HEGIS" => $sub_row->HEGIS_Code,
				"Meta Data"=> $meta,
				"Regional Campuses" => $campuses				
			);
			
			$itemlist[] = $sub_item;		
		}
		
		return $itemlist;
	}
		
}
