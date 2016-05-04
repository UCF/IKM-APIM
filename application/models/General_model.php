<?php
class General_model extends CI_Model
{
	public $tables = array();
	
	function __construct(){
		parent::__construct();
		
		//load configuration
		$this->load->config('main', TRUE);
		
		//setup the tables array from above
		$this->tables  = $this->config->item('tables', 'main');

	}

	function acadplan_all($order,$var_order,$college='%',$career='%',$status='%'){
	
		$this->db->from('Academic_Plans');
		$this->db->join($this->tables['strategic_emphasis'],$this->tables['strategic_emphasis'].'.CIP = Academic_Plans.CIP_Code','left');
		$this->db->join('Degree_Name','Degree_Name.Degree = Academic_Plans.Degree','left');
		$this->db->join('Acad_Prog_Lookup','Acad_Prog_Lookup.Acad_Plan = Academic_Plans.Acad_Plan','left');
		$this->db->where('Plan_Type !=','MIN');
		if($college != '%'){
			$this->db->like('College',$college, 'none', FALSE);
		}
		if($career != '%'){
			$this->db->like('Career',$career, 'none', FALSE);
		}
		if($status != '%'){
			$this->db->like('Status',$status, 'none', FALSE);
		}

		$query = $this->db->get();        
		return $query;

	}
	function subplan_all($plan,$status='%'){
	
		$this->db->from('Sub_Plans');
		$this->db->like('Acad_plan',$plan,'none',FALSE);
		$this->db->like('Status',$status,'none',FALSE);
		
		$query = $this->db->get();        
		return $query;

	}
	function get_term_info_long($long){
		$this->db->from('TERM_XREF');
		$this->db->like('TERM_DESCR',$long,'none',FALSE);
		
		$query = $this->db->get();
		return $query;
		
	}
	function get_access($plan){
		$this->db->select('Acad_Prog,Acad_Plan,Prg_Access');
		$this->db->from('Acad_Prog_Lookup');
		$this->db->like('Acad_Plan',$plan,'none',FALSE);
		$query = $this->db->get();

		return $query;
	}
	function colleges($filter='%'){
		$this->db->from('College_Name');
		
		if($filter != '%'){
			$this->db->like('College', $filter, 'both');
		}
		
		$query = $this->db->get();

		return $query;
	}
	function colleges_batch($data){
		$this->db->from('College_Name');
		$this->db->where_in('College', $data);
		$query = $this->db->get();

		return $query;
	}
	function get_plan_extra($plan){
		
		$this->db->select('*');
		$this->db->from($this->tables['plans_extended']);	
		$this->db->like('Acad_plan',$plan,'none',FALSE);
		$query = $this->db->get();   

		return $query;
	}
	function get_all_plans_cip($cip,$career){
		$this->db->select('Acad_Plan, UCF_Name');
		$this->db->from('Academic_Plans');
		$this->db->where('Plan_Type !=','MIN');
		$this->db->like('CIP_Code',$cip,'none',FALSE);
		$this->db->like('Career',$career,'none',FALSE);
		$this->db->like('Status','A','none',FALSE);
		$query = $this->db->get();
		
		return $query;
		
	}
	function get_all_subplans_cip($plan){
		$this->db->select('DISTINCT Acad_Plan, Sub_Plan, UCF_Name');
		$this->db->from('Sub_Plans');
		$this->db->like('Acad_plan',$plan,'none',FALSE);
		$this->db->like('Status','A','none',FALSE);
		$query = $this->db->get();
	
		return $query;
	
	}
	function insert_plan_extra($batch){
		//use a batch insert
		$this->db->insert($this->tables['plans_extended'], $batch);
	}
	function update_plan_extra($plan,$batch){
		//use a batch insert
		$this->db->like('Acad_Plan',$plan,'none',FALSE);
		$this->db->update($this->tables['plans_extended'], $batch);
	}
	function get_sub_plan_extra($plan,$sub){
		$this->db->select('*');
		$this->db->from($this->tables['subplans_extended']);	
		$this->db->like('Acad_plan',$plan,'none',FALSE);
		$this->db->like('Sub_plan',$sub,'none',FALSE);
		$query = $this->db->get();   

		return $query;
	}
	function insert_sub_plan_extra($batch){
		//use a batch insert
		$this->db->insert($this->tables['subplans_extended'], $batch);
	}
	function update_sub_plan_extra($plan,$sub,$batch){
		//use a batch insert
		$this->db->like('Acad_Plan',$plan,'none');
		$this->db->like('Sub_plan',$sub,'none');
		$this->db->update($this->tables['subplans_extended'], $batch);
	}
	function update_tracker($batch){
		//use a batch insert
		$this->db->insert($this->tables['tracking'], $batch);
	}
	function get_all_regions(){
		$this->db->select('*');
		$this->db->from($this->tables['regional_locations']);
		$query = $this->db->get();

		return $query;
	}
	function plan_locations($plan){
		$this->db->select('*');
		$this->db->from($this->tables['plan_regional_link']);
		$this->db->like('Acad_Plan', $plan, 'none',FALSE);
		$query = $this->db->get();
		
		return $query;
		
	}
	function subplan_locations($plan,$subplan){
		$this->db->select('*');
		$this->db->from($this->tables['subplan_regional_link']);
		$this->db->like('Acad_Plan', $plan, 'none',FALSE);
		$this->db->like('Sub_Plan', $subplan, 'none',FALSE);
		$query = $this->db->get();
	
		return $query;
	
	}
	function delete_plan_region($plan){
		$this->db->where('Acad_Plan',$plan);
		$this->db->delete($this->tables['plan_regional_link']);
	}
	function delete_subplan_region($plan,$subplan){
		$this->db->where('Acad_Plan',$plan);
		$this->db->where('Sub_Plan',$subplan);
		$this->db->delete($this->tables['subplan_regional_link']);
	}
	function insert_plan_region($batch){
		$this->db->insert($this->tables['plan_regional_link'], $batch);		
	}
	function insert_subplan_region($batch){
		$this->db->insert($this->tables['subplan_regional_link'], $batch);
	}
}

 

?>
