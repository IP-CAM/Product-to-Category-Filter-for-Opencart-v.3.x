<?php
class ControllerExtensionModulePsProductCategoryFilter extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/ps_product_category_filter');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_ps_product_category_filter', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/ps_product_category_filter', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/ps_product_category_filter', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_ps_product_category_filter_status'])) {
            $data['module_ps_product_category_filter_status'] = $this->request->post['module_ps_product_category_filter_status'];
        } else {
            $data['module_ps_product_category_filter_status'] = $this->config->get('module_ps_product_category_filter_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/ps_product_category_filter', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/ps_product_category_filter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }


    public function install()
    {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "category_filter` ADD UNIQUE `category_filter_unique` (`category_id`, `filter_id`)");
    }

    public function uninstall()
    {
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "category_filter` DROP INDEX `category_filter_unique`");
    }

    public function cleanupfilter()
	{
        $this->load->language('extension/module/ps_product_category_filter');

		$this->load->model('extension/module/ps_product_category_filter');

		$this->model_extension_module_ps_product_category_filter->cleanupCategoryFilters($this->request->get['category_id']);

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

		$this->response->redirect($this->url->link('catalog/category', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
}
