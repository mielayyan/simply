<?php

require_once 'Inf_Controller.php';

class Report extends Inf_Controller {

    function report_header() {

        $this->set("tran_Welcome_to", $this->lang->line('Welcome_to'));
        $this->set("tran_O", $this->lang->line('O'));
        $this->set("tran_I", $this->lang->line('I'));
        $this->set("tran_Floor", $this->lang->line('Floor'));
        $this->set("tran_em", $this->lang->line('em'));
        $this->set("tran_addr", $this->lang->line('addr'));
        $this->set("tran_comp", $this->lang->line('comp'));
        $this->set("tran_ph", $this->lang->line('ph'));
        $this->set("tran_nfinite", $this->lang->line('nfinite'));
        $this->set("tran_pen", $this->lang->line('pen'));
        $this->set("tran_ource", $this->lang->line('ource'));
        $this->set("tran_olutions", $this->lang->line('olutions'));
        $this->set("tran_S", $this->lang->line('S'));
        $this->set("tran_Date", $this->lang->line('Date'));
        $this->set("tran_email", $this->lang->line('email'));
        $this->set("tran_address", $this->lang->line('address'));
        $this->set("tran_phone", $this->lang->line('phone'));
        $this->set("tran_click_here_print", $this->lang->line('click_here_print'));
    }

    function validate_repurcahse() {
        $this->form_validation->set_rules('week_date1', lang('from_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('to_date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    public function repurchase_report() {
        $columns = [
            0 => 'invoice_no',
            1 => 'total_amount',
            2 => 'payment_method',
            3 => 'order_date',
        ];
        
        $filter = [
          'limit' => $this->input->get('length'),
          'start' => intval($this->input->get("start")),
          'order' => isset($columns[$this->input->get('order')[0]['column']]) ? $columns[$this->input->get('order')[0]['column']] : '',
          'direction' => $this->input->get('order')[0]['dir']
        ];

        /*$daterange = $this->input->get('daterange') ?: 'all';
        dd($daterange)
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('start_date'), $this->input->get('end_date'));
        dump($from_date);*/

        // dd($to_date);
        $from_date = $this->input->get('start_date');
        $to_date   = $this->input->get('end_date');

        $count = $this->report_model->getRepurchaseDetailsCount($from_date, $to_date, $this->LOG_USER_ID);
        $purcahse_details = $this->report_model->getRepurchaseDetailsNew($from_date, $to_date, $this->LOG_USER_ID);

        $data = [];
        $total_amount = 0;
        foreach($purcahse_details as $key => $item) {
            $total_amount += $item->total_amount;
            $data[] = [
                "invoice_no"     => $item->invoice_no,
                'total_amount'   => format_currency($item->total_amount),
                'payment_method' => lang($item->payment_method),
                'purchase_date'  => date("F j, Y, g:i a",strtotime($item->order_date)),
            ];
        }

        if($this->input->get('total_row') && !empty($purcahse_details)) {
            $data[] = [
                "invoice_no"     => lang('total'),
                'total_amount'   => format_currency($total_amount),
                'payment_method' => '',
                'purchase_date'  => '',
            ];
        }
        
        echo json_encode([
            "draw" => intval($this->input->get('draw')),
            "recordsTotal" => intval($count),
            "recordsFiltered" => intval($count),
            "data" => $data
        ]); die;
    }

    function repurchase_report_view() {

        $title = lang('repurchase_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('repurchase_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();

        $this->report_header();
          

          $purcahse_details =array();
          $user_name = $this->LOG_USER_NAME;
          $user_id = $this->report_model->userNameToID($user_name);


        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $dateRangeString .= lang('daterange') . " : (".date('d M Y', strtotime($from_date))." - ".date('d M Y', strtotime($to_date)).")";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/repurchase_report', FALSE);
            }
        }
        
        $this->set('user_name', $user_name);


           if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }



         $purcahse_details = $this->report_model->getRepurchaseDetails($from_date, $to_date, $user_id);
         $count = count($purcahse_details);

        // if ($this->input->post('submit') && $this->validate_repurcahse()) {

        //     $week_date1 = strip_tags($this->input->post('week_date1'));
        //     $this->session->set_userdata("inf_date1", $week_date1);

        //     $week_date2 = strip_tags($this->input->post('week_date2'));
        //     $this->session->set_userdata("inf_date2", $week_date2);

        //     if ($week_date1 == '' && $week_date2 == '') {
        //         $msg = lang('Please select atleast one criteria');
        //         $this->redirect($msg, 'select_report/repurchase_report', FALSE);
        //     }
        //     if ($week_date1 && $week_date2 && $week_date1 > $week_date2) {
        //         $msg = lang('TO date must be greater than or equal to the FROM date');
        //         $this->redirect($msg, 'select_report/repurchase_report', FALSE);
        //     }

        //     $user_name = $this->LOG_USER_NAME;
        //     $this->session->set_userdata("inf_user_name", $user_name);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_repurchase_report_view_error', $error_array);
        //     redirect('user/select_report/repurchase_report');
        // }
        // $report_date = '';
        // if (!empty($this->session->userdata("inf_date1")) && !empty($this->session->userdata("inf_date2"))) {
        //     $report_date = date("Y-m-d H:i:s");

        //     $user_name = $this->session->userdata("inf_user_name");
        //     $user_id = $this->report_model->userNameToID($user_name);

        //     $week_date1 = $this->session->userdata("inf_date1");
        //     $week_date2 = $this->session->userdata("inf_date2");

        //     $purcahse_details = $this->report_model->getRepurchaseDetails($week_date1, $week_date2, $user_id);
        //     $count = count($purcahse_details);
        //     $this->set("count", $count);
        //     $this->set("purcahse_details", $purcahse_details);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_repurchase_report_view_error', $error_array);
        //     redirect('user/select_report/repurchase_report');
        // }

        $help_link = "repurcahse_report_view";
        $this->set("help_link", $help_link);
        $this->set("dateRangeString", $dateRangeString);
        $this->set("report_date", $report_date);
        $this->set("count", $count);
        $this->set("purcahse_details", $purcahse_details);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->setView();
    }

    //E-pin Transfer History Report
    function epin_transfer_report_view() {

        $title = lang('epin_transfer_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $help_link = "epin_transfer_report";

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('epin_transfer_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $user_id = $this->LOG_USER_ID;
        $week_date1='';
        $week_date2='';
        $to_user_name='';

        if ($this->input->post('submit')) {

            // if (!($this->input->post('week_date1')) && !($this->input->post('week_date2')) && !($this->input->post('user_name'))) {
            //     $msg = lang('please_select_date');
            //     $this->redirect($msg, 'select_report/epin_report', FALSE);
            // }

            $to_user_name = strip_tags($this->input->post('user_name', TRUE));
            if (!empty($to_user_name)) {
                if (!($this->report_model->userNameToID($to_user_name))) {
                    $msg = lang('invalid_user_name');
                    $this->redirect($msg, "select_report/epin_report", FALSE);
                }
            }
            $this->session->set_userdata("inf_to_user_name", $to_user_name);

            $user_name = $this->validation_model->IdToUserName($user_id);
            $this->session->set_userdata("inf_user_name", $user_name);

            $week_date1 = strip_tags($this->input->post('week_date1'));
            $this->session->set_userdata("inf_date1", $week_date1);

            $week_date2 = strip_tags($this->input->post('week_date2'));
            $this->session->set_userdata("inf_date2", $week_date2);
        }

        $report_date = date("Y-m-d H:i:s");
        if ($week_date1) {
            if ($week_date2) {
                $report_date = lang('from') . "\t" . $week_date1 . "\t" . lang('to') . "\t" . $week_date2;
            } else {
                $report_date = $week_date1;
            }
        } elseif ($week_date2) {
            $report_date = $week_date2;
        }
        
        $user_name = $this->session->userdata("inf_user_name");
        $to_user_name = empty($this->session->userdata("inf_to_user_name"))?"":$this->session->userdata("inf_to_user_name");
        $to_user_id = ($to_user_name)?$this->report_model->userNameToID($to_user_name):"";

        $week_date1 = empty($this->session->userdata("inf_date1"))?"":$this->session->userdata("inf_date1");
        $week_date2 = empty($this->session->userdata("inf_date2"))?"":$this->session->userdata("inf_date2");

        $transfer_details = $this->report_model->getEpinTransferDetailsForUser($week_date1, $week_date2, $user_id, $to_user_id,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        $count1 = $this->report_model->getCountEpinTransferDetailsForUser($week_date1, $week_date2, $user_id, $to_user_id);
        $this->pagination->set_all('user/epin_transfer_report_view',$count1);
        $count = count($transfer_details);
        
        $dateFromString = ($week_date1)?lang('date_from') . " : " . date("d M Y"):"";
        $dateToString = ($week_date2)?lang('date_to') . " : " . date("d M Y"):"";
        $userString = ($to_user_id)?lang('to_user') . " : " . $this->validation_model->getUserFullName($to_user_id) . "($to_user_name)":"";
        
        $this->set("dateFromString", $dateFromString);
        $this->set("dateToString", $dateToString);
        $this->set("userString", $userString);
        
        $this->set("count", $count);
        $this->set("transfer_details", $transfer_details);
            
        $date = date("Y-m-d");
        $this->set("date", $date);
        $this->set("report_date", $report_date);
        $hdelp_link = "report";
        $this->set("help_link", $help_link);
        $this->setview();
    }

    function sales_report_view() {
        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->url_permission('product_status');
        $this->HEADER_LANG['page_top_header'] = lang('sales_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('sales_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        $this->set("date_submission", lang('date_submission'));
        $report_name = lang('sales_report');
        $this->set('report_name', "$report_name");


        // FILTER
        $date = date("Y-m-d");
        $report_date = '';
        $product_type = '';

        $product_id   = $this->input->get('product_id');
        $package_id   = $this->input->get('prod');
        $product_type = $this->input->get('product_id');

        $daterange    = $this->input->get('daterange') ?: 'all';
        $from_date = $to_date = "";
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        
        if (($from_date != '') && ($to_date != '')) {
            if ($from_date > $to_date) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/sales_report', FALSE);
            }
        }

        if ($from_date != '' && $to_date != '') {
            $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        } else if ($from_date != '') {
            $report_date = lang('from') . "\t" . $from_date;
        } else if ($to_date != '') {
            $report_date = lang('to') . "\t" . $to_date;
        } else {
            $report_date = '';
        }
        


        if ($from_date || $to_date) {
            if ($from_date != '') {
                $from_date = $from_date . " 00:00:00";
            } else {
                $from_date = '';
            }
            if ($to_date != '') {
                $to_date = $to_date . " 23:59:59";
            } else {
                $to_date = '';
            }
        }
        $product_id = $this->input->get('prod') ?: '';

        // DATA
        if ($this->input->get('product_id') == "repurchase") {
            $product_id = $this->input->get('prod') ?: 'all';
            $report_arr = $this->report_model->productRepurchaseSalesReport("all", $from_date, $to_date);
        } else {
            $report_arr = $this->report_model->salesReport($from_date, $to_date, $product_id);
        }
        $product_type = $this->input->get('product_id', TRUE);
        $count = count($report_arr);
        $help_link = "report";
        
        // VIEW
        $this->set('report_arr', $this->security->xss_clean($report_arr));
        $this->set('count', $count);
        $this->set("date", $date);
        $this->set("product_type", $product_type);
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_sales_report_view() {

        $this->form_validation->set_rules('week_date1', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('week_date2', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }
    
    function product_sales_report() {

        $title = lang('report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('sales_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('sales_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();


        $this->set("date_submission", lang('date_submission'));
        $this->set("payment_method", lang('Payment_method'));
        $report_name = lang('sales_report');
        $this->set('report_name', "$report_name");


        $date = date("Y-m-d");
        $this->set("date", $date);
        $product_type = "register";

        if (($this->input->post('user_submit')) && $this->validate_product_sales_report()) {
            $prod_id = (strip_tags($this->input->post('product_id', TRUE)));
            $this->session->set_userdata("inf_product_sales_id", $prod_id);
        } elseif (($this->input->post('user_submit_repurchase')) && $this->validate_product_sales_report()) {
            $prod_id = (strip_tags($this->input->post('product_id', TRUE)));
            $this->session->set_userdata("inf_product_sales_id", $prod_id);
            $product_type = "repurchase";
        } else {
            $error_array_sales = $this->form_validation->error_array();
            $this->session->set_userdata('inf_product_sales_report_error', $error_array_sales);
            redirect('user/select_report/sales_report');
        }
        ///////////////////////////////////
        if ($this->session->userdata("inf_product_sales_id")) {
            $prod_id = $this->session->userdata("inf_product_sales_id");
            if ($product_type == "repurchase")
                $sales_report_arr = $this->report_model->productRepurchaseSalesReport($prod_id);
            else
                $sales_report_arr = $this->report_model->productSalesReport($prod_id);
            $count = count($sales_report_arr);
            $this->set('sales_report_arr', $this->security->xss_clean($sales_report_arr));
            $this->set('count', $count);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_profile_report_view_error', $error_array);
            redirect('user/select_report/sales_report');
        }
        $help_link = "report";
        $this->set("report_date", '');
        $this->set("product_type", $product_type);
        $this->set("help_link", $help_link);
        $this->setView();
    }

    function validate_product_sales_report() {

        $this->form_validation->set_rules("product_id", lang('product'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_released_report_daily() {

        $title = lang('payout_released_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_released_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_released_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $from_date = '';
        $this->set("date", $date);

        if (($this->input->post('payout_released')) && $this->validate_payout_released_report_daily()) {

            $from_date = (strip_tags($this->input->post('week_date1', TRUE)));
            $this->session->set_userdata("inf_released_report_daily", $from_date);
            $ewallt_req_details = $this->report_model->getDailyReleasedPayoutDetails($from_date);
            $count = count($ewallt_req_details);
            $this->set("binary_details", $ewallt_req_details);
            $this->set("count", $count);
        } else {
            $error_array = $this->form_validation->error_array();
            $this->session->set_userdata('inf_payout_released_report_daily_error', $error_array);
            redirect('admin/select_report/payout_release_report');
        }

        $help_link = "downlaod_document";
        $this->set("help_link", $help_link);
        $this->set('report_date', $from_date);
        $this->setView();
    }

    function validate_payout_released_report_daily() {
        $this->form_validation->set_rules('week_date1', lang('date'), 'trim|required|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_released_report_weekly() {

        $title = lang('payout_release_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_release_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();


        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);


        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $dateRangeString .= lang('daterange') . " : (".date('d M Y', strtotime($this->input->get('from_date')))." - ".date('d M Y', strtotime($this->input->get('to_date'))).")";
        }

        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/payout_release_report', FALSE);
            }
        }

         if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }

        $ewallt_req_details = $this->report_model->getReleasedPayoutDetails($from_date, $to_date,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
        
        $count1 = $this->report_model->getCountReleasedPayoutDetails($from_date, $to_date);
        $this->pagination->set_all('user/payout_released_report_weekly',$count1);
        $count = count($ewallt_req_details);


        // if (($this->input->post('payout_released')) && $this->validate_payout_released_report_weekly()) {
        //     if (!($this->input->post('from_date_weekly')) && !($this->input->post('to_date_weekly'))) {
        //         $msg = lang('You_must_select_a_date');
        //         $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //     }
        //     if (($this->input->post('from_date_weekly') != '') && ($this->input->post('to_date_weekly') != '')) {
        //         if (($this->input->post('from_date_weekly')) > ($this->input->post('to_date_weekly'))) {
        //             $msg = lang('To-Date should be greater than From-Date');
        //             $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //         }
        //     }

        //     $from_date = (strip_tags($this->input->post('from_date_weekly', TRUE)));
        //     $to_date = (strip_tags($this->input->post('to_date_weekly', TRUE)));
        //     $this->session->set_userdata("inf_released_report_from_date", $from_date);
        //     $this->session->set_userdata("inf_released_report_to_date", $to_date);
        //     $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        //     $ewallt_req_details = $this->report_model->getReleasedPayoutDetails($from_date, $to_date);
        //     $count = count($ewallt_req_details);
        //     $this->set("binary_details", $ewallt_req_details);
        //     $this->set("count", $count);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_payout_released_report_daily_error', $error_array);
        //     redirect('admin/select_report/payout_release_report');
        // }
        $this->set("payoutFeeDisplay", $this->report_model->checkPayoutFeeDisplay());
        $this->set("report_date", $report_date);
        $this->set("dateRangeString", $dateRangeString);
        $this->set("binary_details", $ewallt_req_details);
        $this->set("count", $count);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->setView();
    }

    function validate_payout_released_report_weekly() {
        $this->form_validation->set_rules('from_date_weekly', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date_weekly', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function payout_pending_report_weekly() {
       
         $title = lang('payout_pending_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $this->HEADER_LANG['page_top_header'] = lang('payout_pending_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('payout_pending_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        $this->report_header();
        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);

      

        


        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $dateRangeString .= lang('daterange') . " : (".date('d M Y', strtotime($this->input->get('from_date')))." - ".date('d M Y', strtotime($this->input->get('to_date'))).")";
        }
        
        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

          if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'select_report/payout_release_report', FALSE);
            }
        }

         if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }
        
         $ewallt_req_details = $this->report_model->getPayoutPendingDetails($from_date, $to_date,$this->input->get('offset'),$this->PAGINATION_PER_PAGE);
  
         $count1 = $this->report_model->getCountPayoutPendingDetails($from_date, $to_date);
         
         $this->pagination->set_all('user/payout_pending_report_weekly',$count1);
         $count = count($ewallt_req_details);

        // if (($this->input->post('payout_released')) && $this->validate_payout_pending_report_weekly()) {

        //     if (!($this->input->post('from_date_pending')) && !($this->input->post('to_date_pending'))) {
        //         $msg = lang('You_must_select_a_date');
        //         $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //     }
        //     if (($this->input->post('from_date_pending') != '') && ($this->input->post('to_date_pending') != '')) {
        //         if (($this->input->post('from_date_pending')) > ($this->input->post('to_date_pending'))) {
        //             $msg = lang('To-Date should be greater than From-Date');
        //             $this->redirect($msg, 'select_report/payout_release_report', FALSE);
        //         }
        //     }
        //     $from_date = (strip_tags($this->input->post('from_date_pending', TRUE)));
        //     $to_date = (strip_tags($this->input->post('to_date_pending', TRUE)));
        //     $this->session->set_userdata("inf_pending_report_from_date", $from_date);
        //     $this->session->set_userdata("inf_pending_report_to_date", $to_date);
        //     if ($from_date != '' && $to_date != '') {
        //         $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
        //     } else if ($from_date != '') {
        //         $report_date = lang('from') . "\t" . $from_date;
        //     } else if ($to_date != '') {
        //         $report_date = lang('to') . "\t" . $to_date;
        //     } else {
        //         $report_date = '';
        //     }
        //     $ewallt_req_details = $this->report_model->getPayoutPendingDetails($from_date, $to_date);
        //     $count = count($ewallt_req_details);
        //     $this->set("binary_details", $ewallt_req_details);
        //     $this->set("count", $count);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_payout_released_report_daily_error', $error_array);
        //     redirect('admin/select_report/payout_release_report');
        // }
       
        $this->set("payoutFeeDisplay", $this->report_model->checkPayoutFeeDisplay());
        $this->set("dateRangeString", $dateRangeString);
        $this->set("report_name", lang('payout_pending_report'));
        $this->set("report_date", $report_date);
        $this->set("binary_details", $ewallt_req_details);
        $this->set("count", $count);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->setview();
    }

    function validate_payout_pending_report_weekly() {

        $this->form_validation->set_rules('from_date_pending', lang('date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date_pending', lang('date'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();
        return $validate_form;
    }

    function commission_report_view() {
        $title = lang('commission_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $date = date("Y-m-d");
        $this->set("date", $date);
        $this->HEADER_LANG['page_top_header'] = lang('commission_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('commission_report');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();
        //Filter
        $daterange = $this->input->get('daterange') ?: 'all';
        $from_date = $to_date = "";
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));
       
        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $dateRangeString .= lang('daterange') . " : (".date('d M Y', strtotime($from_date))." - ".date('d M Y', strtotime($to_date)).")";
        }
        

        // Amount Type
        $typeFilterString = "";
        if ($this->input->get("amount_type") != '') {
            $type = $this->input->get("amount_type", TRUE);
            $i = 0;
            foreach ($type as $t) {
                if ($type[$i] == 'table_fill_commission') {
                    if ($this->MODULE_STATUS['table_status'] == "yes" || $this->MODULE_STATUS['mlm_plan'] == "Board") {
                        $type[$i] = 'board_commission';
                    }
                }
                $typeFilterString .= lang($type[$i]) . ", ";
                $i++;
            }
        } else {
            $type = '';
        }
        if($typeFilterString != "")
            $typeFilterString = lang('amount_type') . " : " . substr($typeFilterString, 0, -2);

         $this->session->set_userdata('inf_commision_type', $type);

        // Other
        $user_name = $this->LOG_USER_NAME;
        $user_id = $this->LOG_USER_ID;

        // Data
        // if ($this->input->get('commision')) {
            // if ($this->input->get('amount_type') == '' && $this->input->get('user_name') == '' && $from_date == '' && $to_date == '') {
            //     $msg = lang('Please Select Atleast One Criteria.');
            //     $this->redirect($msg, 'user/commission_report', FALSE);
            // }

            
        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }




            if (($from_date != '') && ($to_date != '')) {
                if (($from_date) > ($to_date)) {
                    $msg = lang('To-Date should be greater than From-Date');
                    $this->redirect($msg, 'user/commission_report', FALSE);
                }
            }
            
            if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
            }
            
            $details = $this->report_model->getCommisionDetails($type, $from_date, $to_date, $user_id,$this->PAGINATION_PER_PAGE,$this->input->get('offset'));
            
            $count1 = $this->report_model->getCountCommisionDetails($type, $from_date, $to_date, $user_id);
            //print_r($count1);die;
            $this->pagination->set_all('user/commission_report_view',$count1);
            $count = count($details);
        // } else {
        //     $error_array = $this->form_validation->error_array();
        //     $this->session->set_userdata('inf_commission_report_error', $error_array);
        //     redirect('user/commission_report');
        // }

        $this->report_header();
        $total_amount = 0;
        $total_amount_payable = 0;
        $total_tds = 0;
        $total_service_charge = 0;
        foreach($details as $detail) {
            $total_amount += $detail['total_amount'];
            $total_amount_payable += $detail['amount_payable'];
            $total_tds += $detail['tds'];
            $total_service_charge += $detail['service_charge'];
        }
        $help_link = "report";
        
        $showTDS = "yes";
        $showServiceCharge = "yes";
        $showAmountPayable = "yes";
        if(($this->validation_model->getConfig('tds') <= 0) && ($this->report_model->getGrandTotalTDS() <= 0)) {
            $showTDS = "no";
        }
        if(($this->validation_model->getConfig('service_charge') <= 0) && ($this->report_model->getGrandTotalServiceCharge() <= 0)) {
            $showServiceCharge = "no";
        }
        if($showServiceCharge == "no" && $showTDS == "no")
            $showAmountPayable = "no";
        $this->set("showTDS", $showTDS);
        $this->set("showServiceCharge", $showServiceCharge);
        $this->set("showAmountPayable", $showAmountPayable);

        // View
        $this->set('user_name', $user_name);
        $this->set('dateRangeString', $dateRangeString);
        $this->set('typeFilterString', $typeFilterString);
        $this->set('total_amount', $total_amount);
        $this->set('total_amount_payable', $total_amount_payable);
        $this->set('total_tds', $total_tds);
        $this->set('total_service_charge', $total_service_charge);
        $this->set('details', $details);
        $this->set('count', $count);
        $this->set("from_date", $from_date);
        $this->set("to_date", $to_date);
        $this->set('type', $type);
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->setView();
    }

    function validate_commission_report_view() {

        $this->form_validation->set_rules('user_name', lang('user_name'), 'trim|strip_tags');
        $this->form_validation->set_rules('from_date', lang('from_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('to_date', lang('to_date'), 'trim|strip_tags');
        $this->form_validation->set_rules('amount_type[]', lang('amoun_type'), 'trim|strip_tags');
        $validate_form = $this->form_validation->run();

        return $validate_form;
    }

    function getAllProducts(){
        $this->lang->load('select_report_lang');
        $package_type = $this->input->post('package_type');
        $this->load->model('select_report_model');
        if($package_type == 'repurchase'){
         
         $products = $this->select_report_model->getAllProducts('repurchase');
        
        }else{
         $products = $this->select_report_model->getAllProducts();

        }
        echo json_encode($products);exit(); 

    }
     function group_pv_report() {

        $filterUserString = "";
        $dateRangeString = "";

        $title = lang('group_pv_report');
        $this->set("title", $this->COMPANY_NAME . " | $title");

        $date = date("Y-m-d");
        $this->set("date", $date);

        $this->HEADER_LANG['page_top_header'] = lang('group_pv_report');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('group_pv_report');
        $this->HEADER_LANG['page_small_header'] = '';

        $this->load_langauge_scripts();
        
        $date = date("Y-m-d");
        $report_date = '';
        $this->set("date", $date);
        $date1 = date('Y-m-d:H:i:s');
        $user_name = $this->LOG_USER_NAME;
        $user_id = $this->LOG_USER_ID;

        
        //FILTER DATA
        $daterange = $this->input->get('daterange') ?: 'all';
        list($from_date, $to_date) = get_daterange($daterange, $this->input->get('from_date'), $this->input->get('to_date'));

        $dateRangeString = "";
        if($daterange == 'today') {
            $dateRangeString .= lang('daterange') . " : " . lang('today');
        }
        if($daterange == 'month') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_month');
        }
        if($daterange == 'year') {
            $dateRangeString .= lang('daterange') . " : " . lang('this_year');
        }
        if($daterange == 'custom') {
            $fromDate = ($from_date)? date('d M Y', strtotime($from_date)): lang('NA');
            $toDate = ($to_date)? date('d M Y', strtotime($to_date)): lang('NA');
            $dateRangeString .= lang('daterange') . " : ($fromDate - $toDate)";
        }


        if($from_date != ''){
        $from_date = $from_date . " ". "00:00:00";
        }if($to_date != ''){ 
        $to_date = $to_date . " ". "23:59:59";
        }
        

        //validation
        if (($from_date != '') && ($to_date != '')) {
            if (($this->input->get('from_date')) > ($this->input->get('to_date'))) {
                $msg = lang('To-Date should be greater than From-Date');
                $this->redirect($msg, 'user/report/group_pv_report', FALSE);
            }
        }


        $this->set('user_name', $user_name);

        if ($from_date != '' && $to_date != '') {
                $report_date = lang('from') . "\t" . $from_date . "\t" . lang('to') . "\t" . $to_date;
            } else if ($from_date != '') {
                $report_date = lang('from') . "\t" . $from_date;
            } else if ($to_date != '') {
                $report_date = lang('to') . "\t" . $to_date;
            } else {
                $report_date = '';
        }


        //DATA

        $details = $this->report_model->getGroup_pv_details($daterange, $user_id,$from_date, $to_date, $this->PAGINATION_PER_PAGE,$this->input->get('offset'));
        $count1=$this->report_model->getCountGroup_pv_details($daterange, $user_id,$from_date, $to_date);
        $this->pagination->set_all('user/report/group_pv_report',$count1);
        
            
        $count = count($details);
        $this->set("filterUserString", $filterUserString);
        $this->set("dateRangeString", $dateRangeString);

        $this->set('details', $details);
        $this->set('count', $count);
        $this->set('date1', $date1);
        $help_link = "report";
        $this->set("help_link", $help_link);
        $this->set("report_date", $report_date);
        $this->set("from_date", ($from_date)? date('Y-m-d', strtotime($from_date)): '');
        $this->set("to_date",($to_date)? date('Y-m-d', strtotime($to_date)): '');
        $this->set("daterange",$daterange);
        $this->setView();

    }
}
