<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
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
			
			//set the graduate to be false for right now
			$graduate = false;
			$under_graduate = false;
			$default = '';
			
			$s = '';
			$ugs = '';
			$grads = '';
			$prof = '';
			$security_group = array();			
			
			//get user & group information
			$user = $this->ion_auth->user()->row();
			$name = $user->first_name .' '. $user->last_name;
			
			$user_groups = $this->ion_auth->get_users_groups($user->id)->result();
			foreach($user_groups as $ukey => $urow){
					$security_group[] = $urow->name;
				}
			
			//pull the colleges,loop through them, and see if any are in the user group. IF so, use this as
			//the choices ihe user has for the college dropdown.  Default to all colleges if ALL is in group
			$all_group = $this->config->item('all', 'main');			

			if ($this->ion_auth->in_group($all_group)){
				$colleges = $this->General_model->colleges();
				$default = "";
				$s = 'selected';
			} else {
				//this is to remove the all choice 
				$sbit = 0;
				
				//ugrad lock - start certain groups in undergrad
				//get specific college(s)							
				$colleges = $this->General_model->colleges();
				foreach ($colleges->result() as $key => $row){
					if(in_array($row->College,$security_group)){
						$default = $row->College;
					}
				}
								
				if(in_array('GRAD',$security_group)){ $graduate = TRUE; $grads = 'selected'; }
				if($default != "" || in_array('UGRD',$security_group) || in_array('Admissions',$security_group) ){ 
					$under_graduate = TRUE; $ugs = 'selected'; 
				}
				
				if ($graduate === TRUE && $under_graduate === TRUE) { 
					$s = 'selected'; 
					$ugs = '';
					$grads = '';
					$prof = '';
				}
					
			}

			//get the tips
			$tip = $this->config->item('Tips','main');
			
		
			$data['title'] = 'IKM - Academic Program Inventory Manager';
			$data['colleges'] = $colleges;
			$data['name'] = $name;
			$data['default'] = $default;
			$data['s'] = $s;
			$data['ugs'] = $ugs;
			$data['grads'] = $grads;
			$data['prof'] = $prof;
			$data['user_groups'] = $security_group;
			$data['tips'] = $tip;
						
			$this->load->view('main', $data);
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
	
		echo true;
	
	}
	public function update_main(){
		
		
		if (!$this->ion_auth->logged_in()){
		} else {
			//get the user		
			$user = $this->ion_auth->user()->row();
			$name = $user->first_name .' '. $user->last_name;
			$username = $user->username;
			
			//set the column that has been changed
			$cell_under_change = $this->input->get('cellchange');
			
			//get vars coming in from form and set them accordingly
			$plan = $this->input->get('plan');
			$subplan = $this->input->get('subplan');
			$planlongname = $this->input->get('planlongname');
			$subplanlongname = $this->input->get('subplanlongname');
			$deptlongname = $this->input->get('deptlongname');
			$admiss = $this->input->get('adm');
			$readmit = $this->input->get('readmit');
			$flvc = $this->input->get('flvc');
			$orient = $this->input->get('orient');
			$online = $this->input->get('online');
			$psm = $this->input->get('psm');
			$stem = $this->input->get('stem');
			$professional = $this->input->get('professional');
			$mtr = $this->input->get('mtr');
			$cr = $this->input->get('cr');
			$totThesis = $this->input->get('totThesis');
			$tot6971 = $this->input->get('tot6971');
			$totNonThesis = $this->input->get('totNonThesis');
			$totCert = $this->input->get('totCert');
			$totDoc = $this->input->get('totDoc');
			$totDissert = $this->input->get('totDissert');
			$loadas = urldecode($this->input->get('loadas'));
			$main_campus = $this->input->get('main');
			$rosen_campus = $this->input->get('rosen');
			$nona_campus = $this->input->get('lakenona');
			/*$altspring = $this->input->get('ALTSPRING');
			$daytona = $this->input->get('COCOA');
			$daytona = $this->input->get('DAYTONA');
			$leesburg = $this->input->get('LEESBURG');
			$ocala = $this->input->get('OCALA');
			$palmbay = $this->input->get('PALMBAY');
			$lakemary = $this->input->get('LAKEMARY');
			$southlake = $this->input->get('SOUTHLAKE');
			$valencia = $this->input->get('VALENCIA');
			$osceola= $this->input->get('OSCEOLA');
			$metrowest = $this->input->get('METROWEST');*/
	
			//datetime creation
			$date = date("Y-m-d H:i:s");
			
			//some cleanup of the checkboxes
			if($flvc == 'true'){ $flvc = 1; } else { $flvc = 0; }
			if($admiss == 'true'){ $admiss = 1; } else { $admiss = 0; }
			if($readmit == 'true'){ $readmit = 1; } else { $readmit = 0; }
			if($orient == 'true'){ $orient = 1; } else { $orient = 0; }
			if($online == 'true'){ $online = 1; } else { $online = 0; }
			if($psm == 'true'){ $psm = 1; } else { $psm = 0; }
			if($stem == 'true'){ $stem = 1; } else { $stem = 0; }
			if($professional == 'true'){ $professional = 1; } else { $professional = 0; }
			if($mtr == 'true'){ $mtr = 1; } else { $mtr = 0; }
			if($cr == 'true'){ $cr = 1; } else { $cr = 0; }
			if($main_campus == 'true'){ $main_campus = 1; } else { $main_campus = 0; }
			if($rosen_campus == 'true'){ $rosen_campus = 1; } else { $rosen_campus = 0; }
			if($nona_campus == 'true'){ $nona_campus = 1; } else { $nona_campus = 0; }
			/*if($altspring == 'true'){ $altspring = 1; } else { $altspring = 0; }
			if($daytona == 'true'){ $daytona = 1; } else { $daytona = 0; }
			if($leesburg == 'true'){ $leesburg = 1; } else { $leesburg = 0; }
			if($ocala == 'true'){ $ocala = 1; } else { $ocala = 0; }
			if($palmbay == 'true'){ $palmbay = 1; } else { $palmbay = 0; }
			if($lakemary == 'true'){ $lakemary = 1; } else { $lakemary = 0; }
			if($southlake == 'true'){ $southlake = 1; } else { $southlake = 0; }
			if($valencia == 'true'){ $valencia = 1; } else { $valencia = 0; }
			if($osceola == 'true'){ $osceola = 1; } else { $osceolag = 0; }
			if($metrowest == 'true'){ $metrowest = 1; } else { $metrowest = 0; }*/
			
			
			if($cell_under_change != ''){
				//get the official column name under change.  reverse this later from above $batch
				$db_column = $this->corelib->column_name($cell_under_change);
			}
			
			//get the current advanced values for the plan 
			if($subplan == ''){
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
							'cost_recovery' => $cr,
							'professional' => $professional,
							'Total_Thesis' => $totThesis,
							'Total_Thesis6971' => $tot6971,
							'Total_NonThesis' => $totNonThesis,
							'Total_Grad_Certificate' => $totCert,
							'Total_Dissertation' => $totDissert,
							'Total_Doctoral' => $totDoc,
							'Long_Name' => $planlongname,
							'Dept_Long' => $deptlongname,
							'Show_As' => $loadas,
							'Main_Campus' => $main_campus,
							'Rosen_Campus' => $rosen_campus,
							'Lake_Nona_Campus' => $nona_campus
							/*'ALTSPRNG' => $altspring,
							'COCOA' => $daytona,
							'DAYTONA' => $daytona,
							'LEESBURG' => $leesburg,
							'OCALA' => $ocala,
							'PALMBAY' => $palmbay,
							'LAKEMARY' => $lakemary,
							'SOUTHLAKE' => $southlake,
							'VALENCIA' => $valencia,
							'OSCEOLA' => $osceola,
							'METROWEST' => $metrowest*/
					);
				
				
				
				$extra = $this->General_model->get_plan_extra($plan);
				if($extra->num_rows()){
					//update tracker table
					
					//vars coming from interface for tracker piece
					/*$change = array('flvc'=>$flvc,'admiss'=>$admiss,'readmit'=>$readmit,'orient'=>$orient, 'online'=>$online, 'psm'=>$psm, 'stem'=>$stem, 'mtr'=>$mtr, 'cr'=>$cr, 'professional'=>$professional);
					//get the olda
					
					$original_row = $extra->row();
					$original = array('flvc'=>$original_row->FLVC,'admiss'=>$original_row->Admission,'readmit'=>$original_row->Readmit,
							  'online'=>$original_row->Online,'orient'=>$original_row->Orientation, 'stem'=>$original_row->STEM, 'psm'=>$original_row->psm, 'professional'=>$original_row->professional, 
							  'mtr'=>$original_row->MTR,'cr'=>$original_row->cost_recovery);
					
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
					$this->General_model->update_tracker($tracker_batch);*/
					
					//update
					//get the map for the batch
					if($cell_under_change != ''){
						$up_batch_value = $batch[$db_column];
						$up_batch = array($db_column => $up_batch_value);						
						$this->General_model->update_plan_extra($plan,$up_batch);
					} else {
						
						$this->General_model->update_plan_extra($plan,$batch);
					}
													
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
							'cost_recovery' => $cr,
							'professional' => $professional,
							'Total_Thesis' => $totThesis,
							'Total_Thesis6971' => $tot6971,
							'Total_NonThesis' => $totNonThesis,
							'Total_Grad_Certificate' => $totCert,
							'Total_Dissertation' => $totDissert,
							'Total_Doctoral' => $totDoc,
							'Long_Name' => $subplanlongname,
							'Show_As' => $loadas,
							'Main_Campus' => $main_campus,
							'Rosen_Campus' => $rosen_campus,
							'Lake_Nona_Campus' => $nona_campus
							/*'ALTSPRNG' => $altspring,
							'COCOA' => $daytona,
							'DAYTONA' => $daytona,
							'LEESBURG' => $leesburg,
							'OCALA' => $ocala,
							'PALMBAY' => $palmbay,
							'LAKEMARY' => $lakemary,
							'SOUTHLAKE' => $southlake,
							'VALENCIA' => $valencia,
							'OSCEOLA' => $osceola,
							'METROWEST' => $metrowest*/
					);
	
				$extra = $this->General_model->get_sub_plan_extra($plan,$subplan);
				if($extra->num_rows()){
					
					//update
					//get the map for the batch
					if($cell_under_change != ''){
						$up_batch_value = $batch[$db_column];
						$up_batch = array($db_column => $up_batch_value);
						$this->General_model->update_sub_plan_extra($plan,$subplan,$up_batch);
					} else {
						$this->General_model->update_sub_plan_extra($plan,$subplan,$batch);
					}
				} else {
					$this->General_model->insert_sub_plan_extra($batch);
				}
			}
	
			echo true;
		}
		
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
		
		$status = $this->input->get('status');
		if($status == '') { $status = '%'; }
				
		$plan_data = $this->General_model->acadplan_all('ASC','Acad_Plan',$college,$career,$status);	
				
		$itemlist = array();
		$id = 0;
		$auth = 0;
		
		//set the default college for use if a college role is set
		
			$all_group = $this->config->item('all', 'main');
			$user_groups = $this->ion_auth->get_users_groups($user->id)->result();
			if (!$this->ion_auth->in_group($all_group)){
				if($this->ion_auth->in_group('READ') || $this->ion_auth->in_group('Orientation')){
					$auth = 4;
				} else {
					//not in all so look for college
					$colleges = array();
					$collge_setup = array();
					$security_group = array();
					$default_coll = array();

					foreach($user_groups as $ukey => $urow){
						$security_group[] = $urow->name;
						
						if($urow->name == 'GRAD'){ $auth = 2; }
						if($urow->name == 'Online'){ $auth = 5; }
						if($urow->name == 'FLVC'){ $auth = 6; }
						if($urow->name == 'Registrar'){ $auth = 7; }
						if($urow->name == 'Regional'){ $auth = 8; }
						if($urow->name == 'Admissions'){ $auth = 9; } //read only for right now		
						
					}
					$college_get = $this->General_model->colleges();
					foreach ($college_get->result() as $key => $row){
						if(in_array($row->College,$security_group)){
							$default_coll[] = $row->College;
						}
					}
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
			$prog = '';
			$status_change = '';
			$avail = true;
			$regional = '';
			$loadas_arr = array();
			$loadas_arr[] ='';
			$cip_check = '';
			
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
			
			//get the unique plan subplans (based on cip) and add do some array work for addition to final json 
			/* check cip of plan against cip_check var; if different run to get new loas vars
			 * i
			 */
			if($cip_check != $row->CIP_Code){
				$unique_plans_name = $this->General_model->get_all_plans_cip($row->CIP_Code,$row->Career);
					foreach($unique_plans_name->result() as $cip_key => $cip_row){
						//add to array
						$loadas_arr[] = $cip_row->UCF_Name;
				
						//get the subplans
						$unique_subplans_name = $this->General_model->get_all_subplans_cip($cip_row->Acad_Plan);
						//if($unique_subplans_name->num_rows()){
							foreach($unique_subplans_name->result() as $sub_n_key => $sub_n_row){
								$loadas_arr[] = $sub_n_row->UCF_Name;
							}
						//}
					}
				
				$cip_check = $row->CIP_Code;
			}
		
			
			//get the access and Acad_Prog information
			/*$access_data = $this->General_model->get_access($row->Acad_Plan);
			if($access_data->num_rows()){
				//return one row
				$arow = $access_data->row();			
				$access = $arow->Prg_Access;
				$prog = $arow->Acad_Prog;
			}*/ 
			
			//set the prog and access codes
			$access = $row->Prg_Access;
			$prog = $row->Acad_Prog;
			
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
				$cr = 0;
				$professional = 0;
				$totThesis = 0;
				$totNonThesis = 0;
				$tot6971 = 0;
				$totCert = 0;
				$totDoc = 0;
				$totDissert = 0;				
				$recent = '';
				$plan_long_name = '';
				$dept_long_name = '';
				$loadas = '';
				$main = '';
				$rosen = '';
				$nona = '';
			} else {
				$plan_extra_row = $plan_extra->row();
				$plan_long_name = $plan_extra_row->Long_Name;
				$dept_long_name = $plan_extra_row->Dept_Long;
				$admission = $plan_extra_row->Admission;
				$loadas = $plan_extra_row->Show_As;
				$readmit = $plan_extra_row->Readmit;
				$flvc = $plan_extra_row->FLVC;
				$orient = $plan_extra_row->Orientation;
				$online = $plan_extra_row->Online;
				$psm = $plan_extra_row->psm;
				$stem = $plan_extra_row->STEM;	
				$professional = $plan_extra_row->professional;				
				$mtr = $plan_extra_row->MTR;
				$cr = $plan_extra_row->cost_recovery;
				$totThesis = $plan_extra_row->Total_Thesis;
				$totNonThesis = $plan_extra_row->Total_NonThesis;
				$tot6971 = $plan_extra_row->Total_Thesis6971;
				$totDoc = $plan_extra_row->Total_Doctoral;
				$totDissert = $plan_extra_row->Total_Dissertation;
				$tot6971 = $plan_extra_row->Total_Thesis6971;
				$totCert = $plan_extra_row->Total_Grad_Certificate;
				$main = $plan_extra_row->Main_Campus;
				$rosen = $plan_extra_row->Rosen_Campus;
				$nona = $plan_extra_row->Lake_Nona_Campus;
				
				$recent = $plan_extra_row->Recent_Change;
				//$timestamp = strtotime($plan_extra_row->Recent_Change);
				//$recent = date('m/d/yyy', $timestamp);
			
			}
			
			
				if(!empty($default_coll)){
					if(!in_array($row->College,$default_coll)){
						$avail = false;
					}
				} 
				
			
			//fix cert level
			if($row->Degree == 'CRT' || $row->Degree == 'CER'){ $row->Level = 'Certificate'; }
			
			$item = array(	"id" => $id,
					"Plan"=> $row->Acad_Plan,
					"ProgCode" => $prog,
					"PlanLongName"=> $plan_long_name,
					"DeptLongName"=> $dept_long_name,
					"SubPlanLongName" => '',
					"TermStart"=> ucfirst(strtolower($row->Term)),
					"Regional"=> $regional,
					"Stratemph"=> $row->AREA,
					"Subplan" => '',
					"PlanName"=> $row->UCF_Name,
					"College" => $row->College,
					"Dept" => $row->AcadOrg,
					"DeptLong" => $row->AcadOrgDescr,
					"Career" => $row->Career,
					"Level" => $row->Level,
					"CIP" => $row->CIP_Code,
					"HEGIS" => $row->HEGIS_Code,
					"Plan Type" => '',
					"Degree" => $row->Degree,
					"Dept." => $row->AcadOrgDescr,
					"Status" => $row->Status,
					"StatusChange" => $status_change,
					"Access" => $access,
					"Admission" => $admission,
					"loadas" => $loadas,
					"loadasnames" => $loadas_arr,
					"ReAdmit" => $readmit,
					"FLVC" => $flvc,
					"Online" => $online,
					"Orientation" => $orient,
					"PSM" => $psm,
					"STEM" => $stem,
					"Professional" => $professional,
					"MTR" => $mtr,
					"CR"=> $cr,
					"TotThesis" => $totThesis,
					"Tot6971" => $tot6971,
					"TotNonThesis" => $totNonThesis,
					"TotCert" => $totCert,
					"TotDoc" => $totDoc,
					"TotDissert" => $totDissert,
					"MAIN" => $main,
					"ROSEN" => $rosen,
					"NONA" => $nona,
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
					"recentchange" => $recent,
					"avail" => $avail,
					"info" => 'T',
					"check" => $auth
				);
			$itemlist[] = $item;
			
			
			//get the Subs associated with the plans
			$sub_plan_data = $this->General_model->subplan_all($row->Acad_Plan);
			
			if($sub_plan_data->num_rows()){			
				foreach($sub_plan_data->result() as $sub_key => $sub_row){
					$id++;
					$sub_regional = '';
					$adm = array('true','false');
					$admk = array_rand($adm);
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
					
					//set status change date for Inactives and Suspends only
					if($sub_row->Status == 'I' || $sub_row->Status == 'S'){
						$status_change = ucfirst(strtolower($sub_row->Cancelled_Year));
					}
					
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
						$cr = 0;
						$professional = 0;
						$totThesis = 0;
						$totNonThesis = 0;
						$totCert = 0;
						$totDoc = 0;
						$tot6971 = 0;
						$totDissert = 0;
						$sub_long_name = '';
						$loadas = '';
						$main = '';
						$rosen = '';
						$nona = '';
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
						$cr = $subplan_extra_row->cost_recovery;
						$totThesis = $subplan_extra_row->Total_Thesis;
						$totNonThesis = $subplan_extra_row->Total_NonThesis;
						$tot6971 = $subplan_extra_row->Total_Thesis6971;
						$totCert = $subplan_extra_row->Total_Grad_Certificate;
						$totDoc = $subplan_extra_row->Total_Doctoral;
						$totDissert = $subplan_extra_row->Total_Dissertation;
						$loadas = $subplan_extra_row->Show_As;
						$main = $plan_extra_row->Main_Campus;
						$rosen = $plan_extra_row->Rosen_Campus;
						$nona = $plan_extra_row->Lake_Nona_Campus;
					}				
					
					$sub_item = array("id" => $id,
							  "Plan"=> $sub_row->Acad_Plan,
							  "ProgCode" => $prog,
							  "PlanLongName"=> $plan_long_name,
							  "DeptLongName"=> $dept_long_name,
							  "SubPlanLongName"=>$sub_long_name,
							  "TermStart"=> ucfirst(strtolower($sub_row->Term)),
							  "Regional"=>$sub_regional,
							  "Stratemph"=> $row->AREA,
							  "Subplan"=> $sub_row->Sub_Plan,
							  "PlanName"=> $sub_row->UCF_Name,
							  "College" => $row->College,
							  "DeptShort" => $row->AcadOrg,
							  "Dept." => $row->AcadOrgDescr,
							  "Career" => $row->Career,
							  "Level" => $row->Level,
							  "CIP" => $row->CIP_Code,
							  "HEGIS" => $sub_row->HEGIS_Code,
							  "Status" => $sub_row->Status,
							  "StatusChange" => $status_change,
							  "Access" => $access,
							  "Plan Type" => $sub_row->Sub_Pl_Typ,
							  "Degree" => $row->Degree,							  
							  "Admission" => $admission,
							  "loadas" => $loadas,
							  "loadasnames" => $loadas_arr,
							  "ReAdmit" => $readmit,
							  "FLVC" => $flvc,
							  "Orientation" => $orient,
							  "Online" => $online,
							  "PSM" => $psm,
							  "STEM" => $stem,
							  "Professional" => $professional,
							  "MTR" => $mtr,
							  "CR"=> $cr,
							  "TotThesis" => $totThesis,
							  "TotNonThesis" => $totNonThesis,
							  "Tot6971" => $tot6971,
							  "TotCert" => $totCert,
							  "TotDoc" => $totDoc,
							  "TotDissert" => $totDissert,
							  "MAIN" => $main,
							  "ROSEN" => $rosen,
							  "NONA" => $nona,
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
							  "info" => 'T',
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
		$final_data = json_encode($fixed, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	
		//clear the array from memory
		empty($itemlist);
		
		header('Content-Type: text/plain');
		echo  $final_data;
	
	}
	public function tooltip(){
		$data = $this->input->get('tip');
		
		//make the tip name that matches the one in the config file.
		$name = $data."-tip";
		$tip = $this->config->item($name,'main');

		echo $tip;
	}
	public function tooltips(){
		$tip = $this->config->item('Tips','main');
		/*$itemlist = array();
		$tip_array = array();
		foreach ($tip as $key => $val){
				
				$tip_array[] = array(
						$key => $val
				);
			
		}
		
		print_r($tip);*/
		
		$final_data = json_encode($tip, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		
		header('Content-Type: text/plain');
		echo  $final_data;
		
		
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
	public function feedback(){		
		$data['title'] = 'IKM - Academic Program Inventory Manager - Feeback Form';
		$this->load->view('feedback', $data);		
	}
}
