<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Corelib {
	public function utf8_converter($array){
		
		array_walk_recursive($array, function(&$item, $key){
			if(!mb_detect_encoding($item, 'utf-8', true)){
					$item = utf8_encode($item);
			}
		});
		
		return $array;
	}
	
	public function plan_locations($plan)
	{
		$this->CI=& get_instance();
		
		$plan_locations = $this->CI->General_model->plan_locations($plan);
	
		if($plan_locations->num_rows()){
			return $plan_locations;
		} else {
			return false;
		}
	
	}
	
	public function subplan_locations($plan,$subplan)
	{
		$this->CI=& get_instance();
		
		$sublocations = $this->CI->General_model->subplan_locations($plan,$subplan);
		
		if($sublocations->num_rows()){
			return $sublocations;
		} else {
			return false;
		}
	}
	public function column_name($cell)
	{
		//map cell names to column
		$cell_map = array(
				'PlanLongName' => 'Long_Name',
				'SubPlanLongName' => 'Long_Name',
				'Admission' => 'Admission',
				'ReAdmit' => 'Readmit',
				'FLVC' => 'FLVC',
				'orient' => 'Orientation',
				'Online' => 'Online',
				'PSM' => 'psm',
				'STEM' => 'STEM',
				'Professional' => 'professional',
				'MTR' =>  'MTR',
				'CR' => 'cost_recovery',
				'TotThesis' => 'Total_Thesis',
				'Tot6971' => 'Total_Thesis6971',
				'TotNonThesis' => 'Total_NonThesis',
				'TotCert' => 'Total_Grad_Certificate',
				'TotDoc' => 'Total_Doctoral',
				'TotDissert' => 'Total_Dissertation'
		);
		
		return $cell_map[$cell];
	}
	

}
