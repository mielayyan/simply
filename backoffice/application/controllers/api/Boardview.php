<?php

require_once 'Inf_Controller.php';

class BoardView extends Inf_Controller {

    function __construct() {
        parent::__construct();        
        $this->load->model('boardview_model');
    }

    function view_board_details($board_no = '1', $page = '', $limit = '') {     
        $title = lang('board_view');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $langs = array();
        if ($this->MODULE_STATUS['table_status'] == 'yes') {
            $langs['view'] = lang('table_view');
            $langs['name'] = lang('table_name');
            $langs['show_all'] = lang('show_all_tables');
        } else {
            $langs['view'] = lang('board_view');
            $langs['name'] = lang('board_name');
            $langs['show_all'] = lang('show_all_boards');
        }
        $this->HEADER_LANG['page_top_header'] = $langs['view'];
        $this->HEADER_LANG['page_top_small_header'] = lang('');
        $this->HEADER_LANG['page_header'] = $langs['view'];
        $this->HEADER_LANG['page_small_header'] = lang('');

        $this->load_langauge_scripts();

        if ($this->MODULE_STATUS['table_status'] == 'yes') {
            $langs['board_id'] = lang('table_id');
            $langs['board_username'] = lang('table_username');
            $langs['board_split'] = lang('table_split');
            $langs['view_board'] = lang('view_table');
        } else {
            $langs['board_id'] = lang('club_id');
            $langs['board_username'] = lang('club_username');
            $langs['board_split'] = lang('club_split');
            $langs['view_board'] = lang('view_club');
        }

        if ($board_no) {
            $this->load->model('configuration_model');
            $board_no = (int) $board_no;
            $board_config = $this->configuration_model->getBoardSettings($board_no);
            if (!$board_no || !count($board_config)) {
                $this->redirect("Invalid Board!", "boardview/view_board_details", FALSE);
            }
        }

        $board_details["data"] = $this->boardview_model->getSystemBoardDetails();
        echo json_encode($board_details);
    }

function view_user_details_board() {
        $title = lang('board_view');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $langs = array();
        if ($this->MODULE_STATUS['table_status'] == 'yes') {
            $langs['view'] = lang('table_view');
            $langs['name'] = lang('table_name');
            $langs['show_all'] = lang('show_all_tables');
        } else {
            $langs['view'] = lang('board_view');
            $langs['name'] = lang('board_name');
            $langs['show_all'] = lang('show_all_boards');
        }
        $this->HEADER_LANG['page_top_header'] = $langs['view'];
        $this->HEADER_LANG['page_top_small_header'] = lang('');
        $this->HEADER_LANG['page_header'] = $langs['view'];
        $this->HEADER_LANG['page_small_header'] = lang('');

        $this->load_langauge_scripts();
        $post_array = $this->input->post(NULL, TRUE);
        $post_array = $this->validation_model->stripTagsPostArray($post_array);
        $board_no = $post_array['board_id'];
        $page = $post_array['offset'];
        $limit = $post_array['limit'];
        $user_name = $post_array['user_name'];
        $user_id = $this->validation_model->userNameToID($post_array['user_name']);
        $user_board["data"] = $this->boardview_model->getAllBoardDetails($board_no, $page, $limit,$user_id);
        echo json_encode($user_board);
    }
    //board view details 
    public function board_view_get(){
        if($this->MODULE_STATUS['mlm_plan'] != 'Board'){
            $this->set_error_response(422,1057);
        }
        $board_no = $this->get('board_no')==''?1:$this->get('board_no');
        $page = $this->get('page')==''?0:$this->get('board_no');
        $limit = $this->get('limit')==''?$this->PAGINATION_PER_PAGE:$this->get('board_no');
        if ($board_no) {
            $this->load->model('configuration_model');
            $board_no = (int) $board_no;
            $board_config = $this->configuration_model->getBoardSettings($board_no);
            if (!$board_no || !count($board_config)) {
                $this->set_error_response(422,1056);
            }
        }
        $board_details = $this->boardview_model->getSystemBoardDetails();
        $user_id = $this->rest->user_id;
        $total_rows = $this->boardview_model->getAllBoardCount($board_no,$user_id);
        $user_board = $this->boardview_model->getAllBoardDetails($board_no, $page, $limit, $user_id);
        $data = [
            'board_details' => $board_details,
            'total_rows'    => $total_rows,
            'user_board'    => $user_board
        ];
        $this->set_success_response(200,$data);
    }

}
