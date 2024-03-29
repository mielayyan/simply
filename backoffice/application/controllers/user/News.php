<?php

require_once 'Inf_Controller.php';

class News extends Inf_Controller {

    function __construct() {
        parent::__construct();
    }
    function view_all_news()
    {
        $title = lang('view_news_and_events');
        $this->set('title', $this->COMPANY_NAME . " | $title");
        $help_link = 'news-management';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('view_news_and_events');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('view_news_and_events');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $base_url = base_url() . "user/news/view_news";
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->news_model->getAllNewsCount();
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);

        if ($this->news_model->getUnreadNewsCount($this->LOG_USER_ID) > 0) {
            $this->news_model->setNewsViewed($this->LOG_USER_ID);
            $this->set_header_notification_box();
        }
        $news_details = $this->news_model->getAllNews();
        $this->set('news_details', $this->security->xss_clean($news_details));
        $this->set('news_count', count($news_details));
        $this->setView();

    }
    function view_news($page = "") {
        $title = lang('view_news_and_events');
        $this->set('title', $this->COMPANY_NAME . " | $title");
        $help_link = 'news-management';
        $this->set('help_link', $help_link);
        $this->HEADER_LANG['page_top_header'] = lang('view_news_and_events');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('view_news_and_events');
        $this->HEADER_LANG['page_small_header'] = '';
        $this->load_langauge_scripts();

        $base_url = base_url() . "user/news/view_news";
        $config = $this->pagination->customize_style();
        $config['base_url'] = $base_url;
        $config['per_page'] = $this->PAGINATION_PER_PAGE;
        $total_rows = $this->news_model->getAllNewsCount();
        $config['total_rows'] = $total_rows;
        $config["uri_segment"] = 4;
        $this->pagination->initialize($config);

        
        
        $this->set("page", $page);
        if ($this->news_model->getUnreadNewsCount($this->LOG_USER_ID) > 0) {
            $this->news_model->setNewsViewed($this->LOG_USER_ID);
            $this->set_header_notification_box();
        }
        
        if(isset($page))
        {
           $latest_news = $this->news_model->getAllLatestNews($page); 
        }
        else
        {
            $latest_news = $this->news_model->getAllLatestNews();
        }
        $news_details = $this->news_model->getRecentNews($latest_news[0]['news_id']);
        
        $this->set('latest_news', $this->security->xss_clean($latest_news));
        $this->set('news_details', $this->security->xss_clean($news_details));
        $this->set('news_count', count($news_details));
        $this->setView();
    }

    function faq($action = '', $id = '') {

        $title = lang('faqs');
        $this->set("title", $this->COMPANY_NAME . " | $title");
        $this->HEADER_LANG['page_top_header'] = lang('faqs');
        $this->HEADER_LANG['page_top_small_header'] = '';
        $this->HEADER_LANG['page_header'] = lang('faqs');
        $this->HEADER_LANG['page_small_header'] = '';


        $this->load_langauge_scripts();

        $faq = $this->news_model->getBackFAQDetails();
        $this->set("faq", $faq);
        $this->setView();
    }
}
