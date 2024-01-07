<?php

class Excel_register_model extends inf_model
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->LOG_USER_ID) {
            $this->MLM_PLAN = $this->validation_model->getMLMPlan();
        }
        if ($this->MLM_PLAN == 'Hyip' || $this->MLM_PLAN == 'X-Up') {
            $this->load->model('Unilevel_model', 'plan_model');
        }
        else {
            $this->load->model($this->MLM_PLAN . '_model', 'plan_model');
        }
    }

    public function getChildId($father_id, $position)
    {
        return $this->db->select('id')
                        ->where('father_id', $father_id)
                        ->where('position',$position)
                        ->get('ft_individual')
                        ->result_array()[0]['id'] ?? '';
    }
}