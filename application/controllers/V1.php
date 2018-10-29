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
			
						
			if(!isset($filters['status'])){ echo "missing status: e.g. status/all"; exit; }
			if(!isset($filters['college'])){ echo "missing status: e.g. college/all"; exit; }
			if(!isset($filters['career'])){ echo "missing status: e.g. career/all"; exit; }
			if(!isset($filters['location'])){ echo "missing location: e.g. location/all"; exit; }
				
			//parse the url filters
			if($filters['college'] == 'all') { $college = '%'; } else { $college = strtoupper($filters['college']); }	
			if($filters['career'] == 'all') { $career = '%'; } else { $career = strtoupper($filters['career']); }
			if($filters['status'] == 'all') { $status = '%'; } else { $status = strtoupper($filters['status']); }
			
			//capitalize locations
			if(isset($filters['location'])){
				$filters['location'] = strtoupper($filters['location']);
			}
			
			//fix status a bit
			switch($status){
				case 'ACTIVE':
					$status = 'A';
					break;
				case 'INACTIVE':
					$status = 'I';
					break;
				case 'SUSPENDED':
					$status = 'S';
					break;
				case '%':
					break;
					//status is already set in this case
				default:
					$status = 'A';
			}
			
			$api_check = TRUE;
			
			$plan_data = $this->General_model->acadplan_all('ASC','Acad_Plan',$college,$career,$status,$api_check);	//main query
					
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
				$level = '';
				$campuses = array();
				$region_item = array();
				$meta_data = array();
				$meta = array();
				$active_locations = array();
				
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
				$valenciasps = 0;
				
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
					$access = trim($arow->Prg_Access);
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
				
				//set level
				if(empty($row->Level)){ } else { $level = $row->Level; }
				
				
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
							case "VAL-SPS":
								$valenciasps = 1;
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
					$ncsara = 0;
					$psm = 0;
					$stem = 0;
					$mtr = 0;
					$asbs = 0;
					$professional = 0;
					$totThesis = 0;
					$totNonThesis = 0;
					$tot6971 = 0;
					$totCert = 0;
					$totDoc = 0;
					$totDissert = 0;				
					$recent = '';
					$plan_long_name = '';
					$main = 0;
					$rosen = 0;
					$nona = 0;
					$downtown = 0;
					$online_location = 0;
				} else {
					$plan_extra_row = $plan_extra->row();
					$plan_long_name = $plan_extra_row->Long_Name;
					$admission = $plan_extra_row->Admission;
					$readmit = $plan_extra_row->Readmit;
					$flvc = $plan_extra_row->FLVC;
					$orient = $plan_extra_row->Orientation;
					$online = $plan_extra_row->Online;
					$ncsara = $plan_extra_row->NCSARA;
					$psm = $plan_extra_row->psm;
					$asbs = $plan_extra_row->asbs_articulation;
					$stem = $plan_extra_row->STEM;	
					$professional = $plan_extra_row->professional;				
					$mtr = $plan_extra_row->MTR;
					$totThesis = (int)$plan_extra_row->Total_Thesis;
					$totNonThesis = (int)$plan_extra_row->Total_NonThesis;
					$tot6971 = (int)$plan_extra_row->Total_Thesis6971;
					$totDoc = (int)$plan_extra_row->Total_Doctoral;
					$totDissert = (int)$plan_extra_row->Total_Dissertation;
					$tot6971 = (int)$plan_extra_row->Total_Thesis6971;
					$totCert = (int)$plan_extra_row->Total_Grad_Certificate;
					$main = $plan_extra_row->Main_Campus;
					$rosen = $plan_extra_row->Rosen_Campus;
					$nona = $plan_extra_row->Lake_Nona_Campus;
					$downtown = $plan_extra_row->Downtown_Campus;
					$online_location = $plan_extra_row->Online_Location;
	
					$recent = $plan_extra_row->Recent_Change;
					//$timestamp = strtotime($plan_extra_row->Recent_Change);
					//$recent = date('m/d/yyy', $timestamp);
				
				}
				
				//subplan fun
				$sub_data = $this->General_model->subplan_all($row->Acad_Plan,$status);
				if($sub_data->num_rows()){
					$subplan = "Yes";
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
						"VALENCIA" => $valenciaeast,
						"VALSPS" => $valenciasps
				);
				$campuses[] = $region_item; /*****/
				
				$meta_data = array(
						"PlanLongName"=> trim($plan_long_name),
						"TermStart"=> ucfirst(strtolower($row->Term)),
						"TermStartShort" => $termStartShort,
						"Regional"=> $regional,
						"SubPlan"=> $subplan,
						"Stratemph"=> $row->AREA,						
						"Plan Type" => $row->Plan_Type,
						"Degree" => $row->Degree,						
						"Status" => $row->Status,
						"StatusChangeTerm" => $status_change,
						"StatusChangeTermShort" => $statusStartShort,
						"Access" => $access,
						"Admission" => $admission,
						"ReAdmit" => $readmit,
						"FLVC" => $flvc,
						"ASBS" => $asbs,
						"UCFOnline" => $online,
						"NCSARA" => $ncsara,
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
						"TotDissert" => $totDissert,
						"MAIN" => $main,
						"ROSEN" => $rosen,
						"NONA" => $nona,
						"DOWNTOWN" => $downtown
				);
				$meta[] = $meta_data;
				
				if($subplan == "Yes"){
				
					$sub_plans = $this->subplan($sub_data,$row->Acad_Plan,$filters,$meta_data);
				} else {
					
					$sub_plans = array();
				}
				
				//get only the active locations
				if($main == 1){ $active_locations[] = "MAIN"; }
				if($online == 1 || $online_location == 1){ $active_locations[] = "ONLINE"; }
				if($rosen == 1){ $active_locations[] = "ROSEN"; }
				if($nona == 1){ $active_locations[] = "NONA"; }
				if($downtown == 1){ $active_locations[] = "DOWNTOWN"; }
								
				//go through the regional and add the "1"S to the actives
				foreach($region_item as $camp_key => $camp_row){
					if($camp_row == 1){
						$active_locations[] = $camp_key;
					}	
				}
								
				$item = array(	
						"id" => $id,
						"Plan"=> $row->Acad_Plan,
						"PlanName"=> $row->UCF_Name,
						"CollegeShort" => $row->College,
						"College_Full" => $college_name_full,
						"DeptShort" => $row->AcadOrg,
						"Dept_Full" => $row->AcadOrgDescr,
						"Career" => $row->Career,
						"Level" => $level,
						"CIP" => $row->CIP_Code,
						"HEGIS" => $row->HEGIS_Code,
	                    "Meta Data"=> $meta,
						"Regional Campuses" => $campuses,
						"Active Locations" => $active_locations, //array of active locations
						"SubPlans" => $sub_plans
					);
				$itemlist[] = $item;
				
				//FOR CSV OUTPUT
				if(isset($filters['out']) && $filters['out'] == 'csv'){
					$item_csv = array(
							"Plan"=> $row->Acad_Plan,
							"PlanName"=> $row->UCF_Name,
							"CollegeShort" => $row->College,
							"College_Full" => $college_name_full,
							"DeptShort" => $row->AcadOrg,
							"Dept_Full" => $row->AcadOrgDescr,
							"Career" => $row->Career,
							"Level" => $row->Level,
							"CIP" => $row->CIP_Code
					);
					$hegis_item = array(
							"HEGIS" => $row->HEGIS_Code
					);
					$subplan_holder = array(
							"Subplan"=> '',
							"Subplan_Name" => ''
					);
					
					if($meta_data["SubPlan"] == "Yes"){
						$subcheck = 1;
					} else {
						$subcheck = 0;
					}
					
					unset($meta_data["SubPlan"]);
					
					$final_plan_csv = array_merge($item_csv,$subplan_holder,$meta_data,$region_item);
					$csv_itemlist[] = $final_plan_csv;
					//print_r($final_plan_csv);
					
					//dealing with subplans
					if($subcheck == 1){ 
						foreach($sub_plans as $value){
							
							$final_plan_csv = array_merge($item_csv,$value);
							$csv_itemlist[] = $final_plan_csv;
							//print_r($final_plan_csv);
						}
					}
					
					//print_r($final_plan_csv);
													
				}
				
			}
			
			$result_count = count($itemlist);
			
			//fix utf-8 issue with MSSQL
			$fixed = $this->corelib->utf8_converter($itemlist);
			
			//do some work if location flag is set
			//check if location is not all, if it is then continue.  If not, dive into the array and scrub plans/sublans that don't match location
			if($filters['location'] == 'ALL') { } else { 
				$fixed = $this->location($fixed,$filters['location']);
			}
			
			//convert to json and escape any weird chracters
			$final_data = json_encode($fixed, JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); // DON'T JSON_UNESCAPED_SLASHES ON MAIN OUTPUT - WIll break.  ONLY HERE
			
			header('Content-Type: text/plain');
			
			//check of json out was overridden by csv
			if(isset($filters['out']) && $filters['out'] == 'csv'){
						
				// Create a stream opening it with read / write mode
				$stream = fopen('data://text/plain,' . "", 'w+');
				
				// Iterate over the data, writting each line to the text stream
				foreach ($csv_itemlist as $val) {
				    fputcsv($stream, $val);
				}
				
				// Rewind the stream
				rewind($stream);
				
				// You can now echo it's content
				echo stream_get_contents($stream);
				
				// Close the stream 
				fclose($stream);
				
			} else {
				echo  $final_data;
				
			}
		}
	public function location ($input_array, $locat){
		/* Complicated procedure to return plans/subplans by requested location
		 * First pass is to scrub sub=plans that don't match the location and flag its parent plan
		 * Second pass finds top plans that match the location AND that were not flagged before - scrubs the rest
		 */
		
		$plan_keep = array(); //hold plans of subplans that match
		$main_array = $input_array;
		
		//PASS #1 - Subplan cleanse of non-matching locations
		foreach($main_array as $key => &$topvalue){

			$plan_id = $topvalue['Plan'];

			foreach($topvalue['SubPlans'] as $skey => &$subplan){
				$subplan_id = $subplan['Subplan'];
				
				if(!in_array($locat, $subplan['Active Locations'])) {
					//get the non matching subplans out and put the top level plan key in the do not touch array					
					unset($input_array[$key]['SubPlans'][$skey]);
				} else {
					$plan_keep[] = $key;
				}
				
				//array_values($input_array[$key]['SubPlans']);
			}
		}
		
		$plan_arr = $input_array;
		//PASS #2 - Cleanse non-matching plans EXCEPT those that are attached to matchng subs from above
		foreach($plan_arr as $key => &$topvalue){
		
			$plan_id = $topvalue['Plan'];
	
				if(!in_array($key, $plan_keep)){
					if(!in_array($locat, $topvalue['Active Locations'])) {
						//get the non matching subplans out and put the top level plan key in the do not touch array
						unset($input_array[$key]);
					} 
				}
		}
	
		//fix_keys is to reset the array keys after all the resetting done above.
		function fix_keys($array) {
		    $numberCheck = false;
		    foreach ($array as $k => $val) {
		        if (is_array($val)) $array[$k] = fix_keys($val); //recurse
		        if (is_numeric($k)) $numberCheck = true;
		    }
		    if ($numberCheck === true) {
		        return array_values($array);
		    } else {
		        return $array;
		    }
		}
		
		return fix_keys($input_array);
		//echo '<pre>', print_r($input_array), '</pre>';
		
	}
	public function subplan($sub_plan_data,$acad_plan,$filters,$plan_meta){
		foreach($sub_plan_data->result() as $sub_key => $sub_row){
			
			$sub_regional = 'No';
			$adm = array('true','false');
			$admk = array_rand($adm);
			$meta = array();
			$meta_data = array();
			$campuses = array();
			$active_locations = array();
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
			$valenciasps = 0;
			
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
						case "VAL-SPS":
							$valenciasps = 1;
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
				$asbs = 0;
				$orient = 0;
				$online = 0;
				$ncsara = 0;
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
				$main = 0;
				$rosen = 0;
				$nona = 0;
				$downtown = 0;
				$online_location = 0;
			} else {
				$subplan_extra_row = $subplan_extra->row();
				$sub_long_name = $subplan_extra_row->Long_Name;
				$admission = $subplan_extra_row->Admission;
				$readmit = $subplan_extra_row->Readmit;
				$flvc = $subplan_extra_row->FLVC;
				$online = $subplan_extra_row->Online;
				$ncsara = $subplan_extra_row->NCSARA;
				$asbs = $subplan_extra_row->asbs_articulation;
				$orient = $subplan_extra_row->Orientation;
				$psm = $subplan_extra_row->psm;
				$stem = $subplan_extra_row->STEM;
				$professional = $subplan_extra_row->professional;
				$mtr = $subplan_extra_row->MTR;
				$totThesis = (int)$subplan_extra_row->Total_Thesis;
				$totNonThesis = (int)$subplan_extra_row->Total_NonThesis;
				$tot6971 = (int)$subplan_extra_row->Total_Thesis6971;
				$totCert = (int)$subplan_extra_row->Total_Grad_Certificate;
				$totDoc = (int)$subplan_extra_row->Total_Doctoral;
				$totDissert = (int)$subplan_extra_row->Total_Dissertation;
				$main = $subplan_extra_row->Main_Campus;
				$rosen = $subplan_extra_row->Rosen_Campus;
				$nona = $subplan_extra_row->Lake_Nona_Campus;
				$downtown = $subplan_extra_row->Downtown_Campus;
				$online_location = $subplan_extra_row->Online_Location;
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
					"VALENCIA" => $valenciaeast,
					"VALSPS" => $valenciasps
			);
			$campuses[] = $region_item;
			
			$meta_data = array(
			
					"SubPlanLongName"=>$sub_long_name,
					"TermStart"=> ucfirst(strtolower($sub_row->Term)),
					"TermStartShort" => $termStartShort,
					"Regional"=>$sub_regional,
					"Stratemph"=> $plan_meta["Stratemph"],
					"Plan Type" => $sub_row->Sub_Pl_Typ,
					"Degree" => $plan_meta["Degree"],
					"Status" => $sub_row->Status,
					"StatusChangeTerm" => $status_change,
					"StatusChangeTermShort" => $statusStartShort,
					"Access"=> $plan_meta["Access"],
					"Admission" => $admission,
					"ReAdmit" => $readmit,
					"FLVC" => $flvc,
					"ASBS" => $asbs,
					"Orientation" => $orient,
					"UCFOnline" => $online,
					"NCSARA" => $ncsara,
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
					"MAIN" => $main,
					"ROSEN" => $rosen,
					"NONA" => $nona,
					"DOWNTOWN" => $downtown
		
			);
			$meta[] = $meta_data;
			
			//get only the active locations
			if($main == 1){ $active_locations[] = "MAIN"; }
			if($online == 1 || $online_location == 1){ $active_locations[] = "ONLINE"; }
			if($rosen == 1){ $active_locations[] = "ROSEN"; }
			if($nona == 1){ $active_locations[] = "NONA"; }
			if($downtown == 1){ $active_locations[] = "DOWNTOWN"; }
			
			//go through the regional and add the "1"S to the actives
			foreach($region_item as $camp_key => $camp_row){
				if($camp_row == 1){
					$active_locations[] = $camp_key;
				}
			}
			
			$sub_item = array(
				"Subplan"=> trim($sub_row->Sub_Plan),
				"Subplan_Name" => $sub_row->UCF_Name,
				"HEGIS" => $sub_row->HEGIS_Code,
				"Meta Data"=> $meta,
				"Regional Campuses" => $campuses,
				"Active Locations" => $active_locations, //array of active locations
			);
			
			//FOR CSV OUTPUT
			if(isset($filters['out']) && $filters['out'] == 'csv'){
				$sub_item_csv = array(
						"Subplan"=> $sub_row->Sub_Plan,
						"Subplan_Name" => $sub_row->UCF_Name
				);
				
				//remove some elements to make columns line up
				//unset($meta_data[])
				$final_csv = array_merge($sub_item_csv, $meta_data, $region_item);
					
				$itemlist[] = $final_csv;
			} else {
			
				$itemlist[] = $sub_item;
			}
		}
		
		return $itemlist;
	}
		
}
