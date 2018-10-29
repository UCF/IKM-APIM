<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Regional extends CI_Controller {
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
	
	public function index()
	{
		//$this->load->view('main');
		//$colleges = $this->General_model->acadplan_all('ASC','Acad_Plan','CAH','UGRD','A');
		//print_r($colleges);

		if (!$this->ion_auth->logged_in())
		{
			redirect('auth/login', 'refresh');
		} else {
			
			//get user & group information
			$user = $this->ion_auth->user()->row();
			$name = $user->first_name .' '. $user->last_name;
			
			$user_groups = $this->ion_auth->get_users_groups($user->id)->result();
			foreach($user_groups as $ukey => $urow){
				$security_group[] = $urow->name;
			}
			
			//set the all people
			$all_group = $this->config->item('all', 'main');
			
			if(in_array('Regional',$security_group) || $this->ion_auth->in_group($all_group)){
				//get colleges for dropdown
				$colleges = $this->General_model->colleges();
				
				$data['title'] = 'IKM - Program Inventory - Regional Edit';
				$data['colleges'] = $colleges;
				$data['name'] = $name;
									
				$this->load->view('regional-edit', $data);
								
			}
			
		}
	}
	public function regional_association(){
		$plan = '';
		$subplan = '';
		
		$plan = $this->input->get('plan');
		$subplan = $this->input->get('subplan');
		
		/* send this to the view for processing */
	
		$data['plan'] = $plan;
		$data['subplan'] = $subplan;
		
		$this->load->view('regional-edit', $data);		
	}
	public function regional()
	{
		if (!$this->ion_auth->logged_in()){
			redirect('auth/login', 'refresh');
		} else {
			
			//get user information
			$user = $this->ion_auth->user()->row();
			$name = $user->first_name .' '. $user->last_name;
			
			//get colleges for dropdown
			$colleges = $this->General_model->colleges();
				
		
			$data['title'] = 'IKM - Program Inventory - Regional Edit';
			$data['colleges'] = $colleges;
			$data['name'] = $name;
									
			$this->load->view('regional-edit', $data);
		}
	}
	public function update_regional()
	{
		$deletes = array();
		$adds = array();
		$locales = array();

		//get vars coming in from form and set them accordingly
		$plan = $this->input->get('plan');
		$subplan = $this->input->get('subplan');
		
		$ALTSPRNG = $this->input->get('ALTSPRNG');
		$COCOA = $this->input->get('COCOA');
		$DAYTONA = $this->input->get('DAYTONA');
		$LEESBURG = $this->input->get('LEESBURG');
		$OCALA = $this->input->get('OCALA');
		$PALMBAY = $this->input->get('PALMBAY');
		$LAKEMARY = $this->input->get('LAKEMARY');
		$SOUTHLAKE = $this->input->get('SOUTHLAKE');
		$OSCEOLA = $this->input->get('OSCEOLA');
		$METROWEST = $this->input->get('METROWEST');
		$VALENCIA = $this->input->get('VALENCIA');
		
		if($ALTSPRNG == 'true'){ $ALTSPRNG = 1; } else { $ALTSPRNG = 0; }
		if($COCOA == 'true'){ $COCOA = 1; } else { $COCOA = 0; }
		if($DAYTONA == 'true'){ $DAYTONA = 1; } else { $DAYTONA = 0; }
		if($LEESBURG == 'true'){ $LEESBURG = 1; } else { $LEESBURG = 0; }
		if($OCALA == 'true'){ $OCALA = 1; } else { $OCALA = 0; }
		if($PALMBAY == 'true'){ $PALMBAY = 1; } else { $PALMBAY = 0; }
		if($LAKEMARY == 'true'){ $LAKEMARY = 1; } else { $LAKEMARY = 0; }
		if($SOUTHLAKE == 'true'){ $SOUTHLAKE = 1; } else { $SOUTHLAKE = 0; }
		if($OSCEOLA == 'true'){ $OSCEOLA = 1; } else { $OSCEOLA = 0; }
		if($METROWEST == 'true'){ $METROWEST = 1; } else { $METROWEST = 0; }
		if($VALENCIA == 'true'){ $VALENCIA = 1; } else { $VALENCIA = 0; }
		
		//get the variables for the locations and put them into an array based on location and choice
		$region_vars = array("ALTSPRNG","COCOA","DAYTONA","LEESBURG","OCALA","PALMBAY","LAKEMARY","SOUTHLAKE","OSCEOLA","METROWEST","VALENCIA");
		$region_result = compact($region_vars);
			
		
		//put the results into the proper delete or add array - for batch edit
		foreach ($region_result as $reg_key => $reg_row){
			if($reg_key == 'PALMBAY'){ $reg_key = 'PALM BAY'; }
			if($reg_key == 'LAKEMARY'){ $reg_key = 'LAKE MARY'; }
			if($reg_key == 'SOUTHLAKE'){ $reg_key = 'SOUTH LAKE'; }
			
			if($reg_row == 0){
				$deletes[$reg_key] = $reg_row;
			} else {
				$adds[$reg_key] = $reg_row;
			}
		}
		
		
		//datetime creation
		$date = date("Y-m-d H:i:s");
	
		if($subplan == ''){
			//this will edit the plan_region_link table only
			//delete ALL from the table first
			$delete = $this->General_model->delete_plan_region($plan);
			
			//insert set  up and insert
			foreach ($adds as $a_key => $a_row){
				$refid = $plan.'.'.$a_key;
				$locales['Acad_Plan'] = $plan;
				$locales['Location_Code'] = $a_key;
				$locales['REFID'] = $refid;
				
				$insert = $this->General_model->insert_plan_region($locales);				
			}
		} else {
			//this will edit the subplan_region_link table only
			//delete ALL from the table first
			$delete = $this->General_model->delete_subplan_region($plan,$subplan);
			
			//insert set  up and insert
			foreach ($adds as $a_key => $a_row){
				$refid = $plan.'.'.$subplan.'.'.$a_key;
				$locales['Acad_Plan'] = $plan;
				$locales['Sub_Plan'] = $subplan;
				$locales['Location_Code'] = $a_key;
				$locales['REFID'] = $refid;
				
				$insert = $this->General_model->insert_subplan_region($locales);				
			}
			
		}
		
		//get the current advanced values for the plan or subplan in question
		/*if($subplan == ''){
			//set the array
			$batch =  array(
						'Acad_Plan' => $plan,
						'Admission' => $admiss,
						'Readmit' => $readmit,
						'FLVC' => $flvc,
						'Orientation' => $orient,
						'Online' => $online,
						'Recent_Change' => $date,
						'psm' => $psm,
						'STEM' => $stem,
						'MTR' => $mtr,
						'professional' => $professional,
						'Total_Thesis' => $totThesis,
						'Total_Thesis6971' => $tot6971,
						'Total_NonThesis' => $totNonThesis,
						'Total_Grad_Certificate' => $totCert,
						'Total_Dissertation' => $totDissert,
						'Total_Doctoral' => $totDoc

				);

			$extra = $this->General_model->get_plan_extra($plan);
			if($extra->num_rows()){
				//update tracker table
				//vars coming from interface for tracker piece
				$change = array('flvc'=>$flvc,'admiss'=>$admiss,'readmit'=>$readmit,'orient'=>$orient, 'online'=>$online, 'psm'=>$psm, 'stem'=>$stem, '$mtr'=>$mtr, 'professional'=>$professional);
				//get the olda
				
				//foreach($extra->rresult() as $key => $row){ echo $row->Acad_Plan; }
				$original_row = $extra->row();
				$original = array('flvc'=>$original_row->FLVC,'admiss'=>$original_row->Admission,'readmit'=>$original_row->Readmit,
						  'online'=>$original_row->Online,'orient'=>$original_row->Orientation, 'stem'=>$original_row->STEM, 'psm'=>$original_row->psm, 'professional'=>$original_row->professional, 
						  'mtr'=>$original_row->MTR);
				
				//calculate which row has changed.
				$changed = array_diff_assoc($change,$original);
				foreach($changed as $key => $value){
					$variable_changed = $key;
					$variable_to = $value;
				}
				
				$tracker_batch =  array(
						'Acad_Plan' => $plan,
						'Sub_Plan' => $subplan,
						'Username' => $username,
						'Variable' => $variable_changed,
						'Change_To' => $variable_to,
						'Date' => $date
				);
				//update tracker table
				$this->General_model->update_tracker($tracker_batch);
				//update
				$this->General_model->update_plan_extra($plan,$batch);
												
			} else {
				$this->General_model->insert_plan_extra($batch);
			}
					
		} else {
			//set the array
			$batch =  array(
						'Acad_Plan' => $plan,
						'Sub_Plan' => $subplan,
						'Admission' => $admiss,
						'Readmit' => $readmit,
						'FLVC' => $flvc,
						'Orientation' => $orient,
						'Online' => $online,
						'Recent_Change' => $date,
						'psm' => $psm,
						'STEM' => $stem,
						'MTR' => $mtr,
						'professional' => $professional,
						'Total_Thesis' => $totThesis,
						'Total_Thesis6971' => $tot6971,
						'Total_NonThesis' => $totNonThesis,
						'Total_Grad_Certificate' => $totCert,
						'Total_Dissertation' => $totDissert,
						'Total_Doctoral' => $totDoc
				);

			$extra = $this->General_model->get_sub_plan_extra($plan,$subplan);
			if($extra->num_rows()){
				//update
				//figure out which got changed
				$this->General_model->update_sub_plan_extra($plan,$subplan,$batch);
			} else {
				$this->General_model->insert_sub_plan_extra($batch);
			}
		}*/

		echo true;
		
	}
	public function data_main()
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
		$reg_locations = array();
		
		$id = 0;
		$auth = 0;
		
		//set the default college for use if applicable
		
			$all_group = $this->config->item('all', 'main','Regional');
			$user_groups = $this->ion_auth->get_users_groups($user->id)->result();
			if (!$this->ion_auth->in_group($all_group)){
				if($this->ion_auth->in_group('READ')){
					$auth = 4;
				} else {
					//not in all so look for college
					/*$colleges = array();
					$collge_setup = array();
					$security_group = array();
					$default_coll = array();

					foreach($user_groups as $ukey => $urow){
						$security_group[] = $urow->name;
						
						if($urow->name == 'GRAD'){ $auth = 2; }
					}
					$college_get = $this->General_model->colleges();
					foreach ($college_get->result() as $key => $row){
						if(in_array($row->College,$security_group)){
							$default_coll[] = $row->College;
						}
					}*/
				}
			} else {
				$auth = 1; //keep as one and reset below if necessary
				foreach($user_groups as $ukey => $urow){
					if($urow->name == 'All'){ 
						$auth = 3; 
					}
					if($urow->name ==  'READ'){
						$auth = 4;						
					}
				}
			}
		
		foreach ($plan_data->result() as $key => $row){
			$id++;			
			$access = '';
			$status_change = '';
			$avail = true;		
			
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
			
			//get the regional locations
			/*$regional_locations = $this->General_model->get_all_regions();
			foreach ($regional_locations->result() as $rkey => $rrow){
				$reg_locations[] = $rrow->Location_Code;
			}*/
			
			//get all the locations associated with the top level plan
			$plan_locations = $this->General_model->plan_locations($row->Acad_Plan);
			if($plan_locations->num_rows()){
		
				//loop through the returning array and then use the case switch to set the flag for the grid
				foreach ($plan_locations->result() as $pl_key => $pl_row){
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
			} else {
				$plan_extra_row = $plan_extra->row();
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
			
			
			if(!empty($default_coll)){
				if(!in_array($row->College,$default_coll)){
					$avail = false;
				}
			} 
				
			
				
			
			$item = array(	"id" => $id,
					"Plan"=> $row->Acad_Plan,
					"TermStart"=> ucfirst(strtolower($row->Term)),
					"Stratemph"=> $row->AREA,
					"Subplan" => '',
					"Plan Name"=> $row->UCF_Name,
					"College" => $row->College,
					"Career" => $row->Career,
					"CIP" => $row->CIP_Code,
					"HEGIS" => $row->HEGIS_Code,
					"Plan Type" => '',
					"Degree" => $row->Degree,
					"Dept." => $row->AcadOrgDescr,
					"Status" => $row->Status,
					"StatusChange" => $status_change,
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
					"TotThesis" => $totThesis,
					"Tot6971" => $tot6971,
					"TotNonThesis" => $totNonThesis,
					"TotCert" => $totCert,
					"TotDoc" => $totDoc,
					"TotDissert" => $totDissert,					
					"recentchange" => $recent,
					"avail" => $avail,
					"info" => 'T',
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
					"check" => $auth
				);
			$itemlist[] = $item;
			
			
			//get the Subs associated with the plans
			$sub_plan_data = $this->General_model->subplan_all($row->Acad_Plan);
			
			if($sub_plan_data->num_rows()){			
				foreach($sub_plan_data->result() as $sub_key => $sub_row){
					$id++;
			
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
					
					//get the locations associated with subplan
					$subplan_locations = $this->General_model->subplan_locations($row->Acad_Plan,$sub_row->Sub_Plan);
					if($subplan_locations->num_rows()){
					
						//loop through the returning array and then use the case switch to set the flag for the grid
						foreach ($subplan_locations->result() as $spl_key => $spl_row){
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
					} else {
						$subplan_extra_row = $subplan_extra->row();

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
							  "TermStart"=> ucfirst(strtolower($sub_row->Term)),
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
							  "TotThesis" => $totThesis,
							  "TotNonThesis" => $totNonThesis,
							  "Tot6971" => $tot6971,
							  "TotCert" => $totCert,
							  "TotDoc" => $totDoc,
							  "TotDissert" => $totDissert,
							  "avail" => $avail,
							  "info" => 'T',
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
							  "check" => $auth
						);
					$itemlist[] = $sub_item;
			
				}
			}
		}
		
		$result_count = count($itemlist);
		
		//fix utf-8 issue with MSSQL
		$fixed = $this->corelib->utf8_converter($itemlist);

		//convert to json and escape any weird chracters
		$final_data = json_encode($fixed, JSON_UNESCAPED_UNICODE);
	
		echo  $final_data;
		
		
		//echo  '{"total":"1","page":"1","records":"' . $result_count . '","rows":' . $final_data . '}';
		/*$data  = json_encode($itemlist);
		echo '{"totalCount":"' . $result_count . '","period":"' . $term_name . '","results":' . $data . '}';*/
	}
	public function tooltip(){
		$data = $this->input->get('tip');
		
		//make the tip name that matches the one in the config file.
		$name = $data."-tip";
		$tip = $this->config->item($name,'main');

		echo $tip;
	}
	public function extra_data(){
		$plan = $this->input->get('plan');
		$subplan = $this->input->get('subplan');
		
		echo $plan . " " . $subplan . "<p>Other things</p>";
	}
	public function export()
	{
	
		$data['export'] = $career = $this->input->get('data');
		$data['filename'] = 'Exported List';	
		$this->load->view('export', $data);
	}
}
