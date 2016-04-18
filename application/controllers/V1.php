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

	public function main()
		{
			$final_data = '';
		
			//get user information
			$user = $this->ion_auth->user()->row();
			
			//get the uri call
			$filters = $this->uri->uri_to_assoc(3);
				
			
			if($filters['college'] == 'all') { $college = '%'; } else { $college = strtoupper($filters['college']); }
			
		
			if($filters['career'] == 'all') { $career = '%'; } else { $career = strtoupper($filters['career']); }
					
			$plan_data = $this->General_model->acadplan_all('ASC','Acad_Plan',$college,$career);	
					
			$itemlist = array();
			$id = 0;
					
						
			foreach ($plan_data->result() as $key => $row){
				$id++;			
				$access = '';
				$termStartShort = '';
				$statusStartShort = '';
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
				
				//get start term and status term change short (if applicable);
				if($row->Term != ''){
					$term_s = ucfirst(strtolower($row->Term));
					$term_short = $this->General_model->get_term_info_long($term_s);
					
					if($term_short->num_rows()){
						$trow = $term_short->row();						
						$termStartShort = $trow->PS_STRM;
					}
					
				}
				
				
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
					
					//cancelled year
					if($row->Cancelled_Year != ''){
						
						$cterm_s = ucfirst(strtolower($row->Cancelled_Year));
						$cterm_short = $this->General_model->get_term_info_long($cterm_s);
					
						if($cterm_short->num_rows()){
							$crow = $cterm_short->row();
							$statusStartShort = $crow->PS_STRM;
						}
					
					}
				}
				
				//get college name
				$college_name = $this->General_model->colleges($row->College);
				if($college_name->num_rows()){
					$coll_row =  $college_name->row();
					$college_name_full = $coll_row->College_Name;					
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
				
				//fix certs
				if($row->Degree == 'CRT' || $row->Degree == 'CER'){ $row->Level = 'Certificate'; }
				
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
						"TermStartShort" => $termStartShort,
						"Regional"=> $regional,
						"SubPlan"=> $subplan,
						"PlanLongName"=> $plan_long_name,
						"Stratemph"=> $row->AREA,						
						"Plan Type" => '',
						"Degree" => $row->Degree,						
						"Status" => $row->Status,
						"StatusChangeTerm" => $status_change,
						"StatusChangeTermShort" => $statusStartShort,
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
						"CollegeShort" => $row->College,
						"College_Full" => $college_name_full,
						"DeptShort" => $row->AcadOrg,
						"Dept_Full" => $row->AcadOrgDescr,
						"Career" => $row->Career,
						"Level" => $row->Level,
						"CIP" => $row->CIP_Code,
						"HEGIS" => $row->HEGIS_Code,
	                    "Meta Data"=> $meta,
						"Regional Campuses" => $campuses,
						"SubPlans" => $sub_plans
					);
				$itemlist[] = $item;
				
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
			$termStartShort = '';
			$statusStartShort = '';
			$status_change = '';
		
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
			
			//get start term and status term change short (if applicable);
			if($sub_row->Term != ''){
				$term_s = ucfirst(strtolower($sub_row->Term));
				$term_short = $this->General_model->get_term_info_long($term_s);
					
				if($term_short->num_rows()){
					$trow = $term_short->row();
					$termStartShort = $trow->PS_STRM;
				}
					
			}
				
		
			//set status change date for Inactives and Suspends only
			if($sub_row->Status == 'I' || $sub_row->Status == 'S'){
				$status_change = ucfirst(strtolower($sub_row->Cancelled_Year));
					
				//cancelled year
				if($sub_row->Cancelled_Year != ''){
			
					$cterm_s = ucfirst(strtolower($sub_row->Cancelled_Year));
					$cterm_short = $this->General_model->get_term_info_long($cterm_s);
						
					if($cterm_short->num_rows()){
						$crow = $cterm_short->row();
						$statusStartShort = $crow->PS_STRM;
					}
						
				}
			}
			
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
					"TermStartShort" => $termStartShort,
					"Regional"=>$sub_regional,					
					"Status" => $sub_row->Status,
					"StatusChangeTerm" => $status_change,
					"StatusChangeTermShort" => $statusStartShort,
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
