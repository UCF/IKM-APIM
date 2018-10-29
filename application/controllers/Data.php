<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends CI_Controller {
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
		
		$this->load->helper(array('url','language'));

		$this->load->config('main', TRUE);
		
	}
	
	
	public function get_programs()
	{
		
		$college = $this->input->get('college');
		$career = $this->input->get('career');
		$status = $this->input->get('status');
		
		if($college == 'all'){ $college = ''; }
		if($status == 'all'){ $status = ''; }

		$plan_data = $this->General_model->acadplan_all('ASC','Acad_Plan',$college,$career,$status);	
		
		$itemlist = array();
		$id = 0;
		
		//set the default college for use if applicable		
			

		foreach ($plan_data->result() as $key => $row){
			$id++;			
			$access = '';
			$avail = true;		
			
			//get the access information
			$access_data = $this->General_model->get_access($row->Acad_Plan);
			if($access_data->num_rows()){
				//return one row
				$arow = $access_data->row();			
				$access = $arow->Prg_Access;
			} 
			
			//get the plan extras; set zeros if no record exists
			$plan_extra = $this->General_model->get_plan_extra($row->Acad_Plan);
			if(!$plan_extra->num_rows()){
				$admission = 0;
				$readmit = 0;
				$flvc = 0;
				$orient = 0;
				$online = 0;
				$stem = 0;
				$psm = 0;
				$professional = 0;
				$mtr = 0;
				$recent = '';
			} else {
				$plan_extra_row = $plan_extra->row();

				$admission = $plan_extra_row->Admission;
				$readmit = $plan_extra_row->Readmit;
				$flvc = $plan_extra_row->FLVC;
				$orient = $plan_extra_row->Orientation;
				$online = $plan_extra_row->Online;
				$stem = $plan_extra_row->STEM;
				$psm = $plan_extra_row->psm;
				$professional = $plan_extra_row->professional;
				$mtr = $plan_extra_row->MTR;
				
				$recent = $plan_extra_row->Recent_Change;
				/*$timestamp = strtotime($plan_extra_row->Recent_Change);
				$recent = date('m/d/yyy', $timestamp);*/
			}
			
			

				if(!empty($default_coll)){
					if(!in_array($row->College,$default_coll)){
						$avail = false;
					}
				} 
			
			$item = array(	"id" => $id,
					"Plan"=> $row->Acad_Plan,
					"Subplan" => '',
					"Career" => $row->Career,
					"Plan Name"=> $row->UCF_Name,
					"College" => $row->College,
					"CIP" => $row->CIP_Code,
					"HEGIS" => $row->HEGIS_Code,
					"Plan Type" => '',
					"Degree" => $row->Degree,
					"Dept." => $row->AcadOrgDescr,
					"Status" => $row->Status,
					"Access" => $access,
					"Admission" => $admission,
					"ReAdmit" => $readmit,
					"FLVC" => $flvc,
					"Online" => $online,
					"STEM" => $stem,
					"PSM" => $psm,
					"Professional" => $professional,
					"Mrkt. Tuition" => $mtr,
					"Orientation" => $orient,
					"recentchange" => $recent,
					"avail" => $avail,
					"info" => 'T'
				);
			$itemlist[] = $item;

			//get the Subs associated with the plans
			$sub_plan_data = $this->General_model->subplan_all($row->Acad_Plan,$status);
			if($sub_plan_data->num_rows()){			
				foreach($sub_plan_data->result() as $sub_key => $sub_row){
					$id++;
			
					$adm = array('true','false');
					$admk = array_rand($adm);
					
					//get the plan extras; set zeros if no record exists
					$subplan_extra = $this->General_model->get_sub_plan_extra($row->Acad_Plan,$sub_row->Sub_Plan);
					if(!$subplan_extra->num_rows()){
						$admission = 0;
						$readmit = 0;
						$flvc = 0;
						$orient = 0;
						$online = 0;
						$stem = 0;
						$psm = 0;
						$professional = 0;
						$mtr = 0;
					} else {
						$subplan_extra_row = $subplan_extra->row();

						$admission = $subplan_extra_row->Admission;
						$readmit = $subplan_extra_row->Readmit;
						$flvc = $subplan_extra_row->FLVC;
						$online = $subplan_extra_row->Online;
						$orient = $subplan_extra_row->Orientation;
						$stem = $subplan_extra_row->STEM;
						$psm = 'tt';
						$professional = $subplan_extra_row->professional;
						$mtr = $subplan_extra_row->MTR;
					}				
					
					$sub_item = array("id" => $id,
							  "Plan"=> $sub_row->Acad_Plan,
							  "Subplan"=> $sub_row->Sub_Plan,
							  "Plan Name"=> $sub_row->UCF_Name,
							  "College" => $row->College,
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
							  "STEM" => $stem,
							  "PSM" => $psm,
							  "Professional" => $professional,
							  "Mrkt. Tuition" => $mtr,
							  "Orientation" => $orient,
							  "Online" => $online,
							  "avail" => $avail,
							  "info" => 'T'
						);
					$itemlist[] = $sub_item;
			
				}
			}
		}
		
		$result_count = count($itemlist);

		$final_data  = json_encode($itemlist, JSON_PRETTY_PRINT);
		
		printf("<pre>%s</pre>",  $final_data);
		//echo  '{"total":"1","page":"1","records":"' . $result_count . '","rows":' . $final_data . '}';
			
		/*$data  = json_encode($itemlist);
		echo '{"totalCount":"' . $result_count . '","period":"' . $term_name . '","results":' . $data . '}';*/
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
