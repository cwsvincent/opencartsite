<?php
class ControllerExtensionDashboardOrder extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/dashboard/oku');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=dashboard', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/dashboard/oku', 'user_token=' . $this->session->data['user_token'], true)
		);

		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/dashboard/oku')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function dashboard() {
		$this->load->language('extension/dashboard/oku');

		$data['user_token'] = $this->session->data['user_token'];

		// Get Total records
		$this->load->model('okupage/oku');


		$oku_total = $this->model_okupage_oku->getTotalOku();

		if ($oku_total > 1000000000000) {
			$data['total'] = round($oku_total / 1000000000000, 1) . 'T';
		} elseif ($oku_total > 1000000000) {
			$data['total'] = round($oku_total / 1000000000, 1) . 'B';
		} elseif ($oku_total > 1000000) {
			$data['total'] = round($oku_total / 1000000, 1) . 'M';
		} elseif ($oku_total > 1000) {
			$data['total'] = round($oku_total / 1000, 1) . 'K';
		} else {
			$data['total'] = $oku_total;
		}

		$data['oku'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'], true);

		return $this->load->view('extension/dashboard/oku_info', $data);
	}
}

