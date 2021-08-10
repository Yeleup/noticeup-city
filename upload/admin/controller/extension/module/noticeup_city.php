<?php

class ControllerExtensionModuleNoticeupCity extends Controller
{
    private $error = array();

    public function uninstall()
    {
        $this->db->table("setting")->where('code','module_noticeup_city')->delete();

        $this->db->query("DROP TABLE " . DB_PREFIX . "noticeup_city");
    }

    public function install()
    {
        $this->db->table("setting")->add([
            'code' => 'module_noticeup_city',
            'key' => 'module_noticeup_city_status',
            'value' => '1',
        ]);

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "noticeup_city` (
                `city_id` INT(10) NOT NULL AUTO_INCREMENT,
                `zone_id` INT(10) NOT NULL,
                `name` VARCHAR(64) NOT NULL COLLATE 'utf8_general_ci',
                `status` TINYINT(1) NOT NULL DEFAULT '0',
                `sort_order` INT(10) NOT NULL DEFAULT '0',
                PRIMARY KEY (`city_id`, `zone_id`) USING BTREE
            )
            COLLATE='utf8_general_ci'
            ENGINE=MyISAM
            AUTO_INCREMENT=1
            ;
        ");
    }

    public function index()
    {
        $this->load->language('extension/module/noticeup_city');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->getList();
    }

    public function add()
    {
        $this->load->language('extension/module/noticeup_city');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->db->table('noticeup_city')->add([
                'name' => $this->request->post['name'],
                'zone_id' => $this->request->post['zone_id'],
                'sort_order' => $this->request->post['sort_order'],
                'status' => $this->request->post['status'],
            ]);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit()
    {
        $this->load->language('extension/module/noticeup_city');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->db->table('noticeup_city')->find($this->request->get['city_id'])->set([
                'name' => $this->request->post['name'],
                'zone_id' => $this->request->post['zone_id'],
                'sort_order' => $this->request->post['sort_order'],
                'status' => $this->request->post['status'],
            ]);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete()
    {
        $this->load->language('extension/module/noticeup_city');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $city_id) {
                $this->db->table("noticeup_city")->find($city_id)->delete();
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList()
    {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'c.name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('extension/module/noticeup_city/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
        $data['delete'] = $this->url->link('extension/module/noticeup_city/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['cities'] = array();

        $zone_total = $this->db->table("noticeup_city")->count();

        $query = $this->db->table('noticeup_city c')->leftJoin('zone z', 'z.zone_id', 'c.zone_id');

        $sort_data = array(
            'c.name',
            'z.name',
            'c.sort_order'
        );

        if (isset($sort) && in_array($sort, $sort_data)) {
            $query->sortBy($sort, $order);
        } else {
            $query->sortBy('c.name', $order);
        }

        $query->limit($this->config->get('config_limit_admin'))->page($page);

        $results = $query->get(['c.sort_order' => 'sort_order', 'c.status' => 'status', 'c.zone_id', 'c.city_id', 'c.name' => 'name', 'z.name' => 'zone']);

        foreach ($results as $result) {
            $data['cities'][] = array(
                'city_id' => $result['city_id'],
                'zone' => $result['zone'],
                'name' => $result['name'],
                'sort_order' => $result['sort_order'],
                'edit' => $this->url->link('extension/module/noticeup_city/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url, true)
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_zone'] = $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . '&sort=z.name' . $url, true);
        $data['sort_name'] = $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . '&sort=c.name' . $url, true);
        $data['sort_sort_order'] = $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . '&sort=c.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $zone_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($zone_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($zone_total - $this->config->get('config_limit_admin'))) ? $zone_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $zone_total, ceil($zone_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/noticeup_city_list', $data));
    }

    protected function getForm()
    {
        $data['text_form'] = !isset($this->request->get['city_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['city_id'])) {
            $data['action'] = $this->url->link('extension/module/noticeup_city/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/noticeup_city/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $this->request->get['city_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/noticeup_city', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['city_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $zone_info = $this->db->table('noticeup_city')->find($this->request->get['city_id'])->get();
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($zone_info)) {
            $data['status'] = $zone_info['status'];
        } else {
            $data['status'] = '1';
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($zone_info)) {
            $data['name'] = $zone_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($zone_info)) {
            $data['sort_order'] = $zone_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        if (isset($this->request->post['country_id'])) {
            $data['zone_id'] = $this->request->post['zone_id'];
        } elseif (!empty($zone_info)) {
            $data['zone_id'] = $zone_info['zone_id'];
        } else {
            $data['zone_id'] = '';
        }

        $data['zones'] = $this->db->table("zone")->get();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/noticeup_city_form', $data));
    }

    protected function validateForm()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/noticeup_city')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        return !$this->error;
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/noticeup_city')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}