<?php
class ControllerOkupageOku extends Controller { 
	private $error = array();

	public function index() {
		//load model and lang file
		$this->load->model('okupage/oku');
		$this->load->language('okupage/oku');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->getList();
	}

	protected function getList() {

		//laod dompdf
		$this->load->library('pdf');
		$this->load->language('okupage/oku');

		//filter part
		if (isset($this->request->get['filter_oku_id'])) {
			$filter_oku_id = $this->request->get['filter_oku_id'];
		} else {
			$filter_oku_id = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_passport_no'])) {
			$filter_passport_no = $this->request->get['filter_passport_no'];
		} else {
			$filter_passport_no = '';
		}

		if (isset($this->request->get['filter_gender'])) {
			$filter_gender = $this->request->get['filter_gender'];
		} else {
			$filter_gender = '';
		}

		if (isset($this->request->get['filter_nationality'])) {
			$filter_nationality = $this->request->get['filter_nationality'];
		} else {
			$filter_nationality = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category_id'];
		} else {
			$filter_category = '';
		}

		if (isset($this->request->get['filter_justification'])) {
			$filter_justification = $this->request->get['filter_justification'];
		} else {
			$filter_justification = '';
		}

		if (isset($this->request->get['filter_loname'])) {
			$filter_loname = $this->request->get['filter_loname'];
		} else {
			$filter_loname = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}


		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ok.oku_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_oku_id'])) {
			$url .= '&filter_oku_id=' . $this->request->get['filter_oku_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_passport_no'])) {
			$url .= '&filter_passport_no=' . $this->request->get['filter_passport_no'];
		}

		if (isset($this->request->get['filter_gender'])) {
			$url .= '&filter_gender=' . $this->request->get['filter_gender'];
		}

		if (isset($this->request->get['filter_nationality'])) {
			$url .= '&filter_nationality=' . $this->request->get['filter_nationality'];
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_justification'])) {
			$url .= '&filter_justification=' . $this->request->get['filter_justification'];
		}

		if (isset($this->request->get['filter_loname'])) {
			$url .= '&filter_loname=' . $this->request->get['filter_loname'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

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
			'href' => $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['selected'] = str_replace('&amp;', '&', $this->url->link('okupage/oku/selected', 'user_token=' . 
		$this->session->data['user_token'] . $url, true));

		$data['category'] = array();

		$filter_data = array(
			'filter_oku_id'            => $filter_oku_id,
			'filter_name'	     	   => $filter_name,
			'filter_passport_no'       => $filter_passport_no,
			'filter_gender'            => $filter_gender,
			'filter_nationality'       => $filter_nationality,
			'filter_category_id'       => $filter_category_id,
			'filter_category'          => $filter_category,
			'filter_justification'     => $filter_justification,
			'filter_loname'            => $filter_loname,
			'filter_date_added'        => $filter_date_added,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		//retrieve total no. and total list from database
		$oku_total = $this->model_okupage_oku->getTotalOku($filter_data);
		$results = $this->model_okupage_oku->getOku($filter_data);

		//retrive text of genders from lang file
		$text_male = $this->language->get('text_male');
		$text_female = $this->language->get('text_female');


		foreach ($results as $result) {

			//convert gender_id into a text string
			if ($result['gender'] == 1 || $result['gender'] == '1') {
				$result['gender'] = $text_male;
			}else {
				$result['gender'] = $text_female;
			}
			$data['oku'][] = array(
				'oku_id'      	  => $result['oku_id'],
				'name'     		  => $result['name'],
				'passport_no'     => $result['passport_no'],
				'gender'          => $result['gender'],
				'nationality'     => $result['nationality'],
				'category_id'     => $result['category_id'],
				'category'        => $result['category'],
				'justification'   => $result['justification'],
				'loname'          => $result['lo_name'],
				'date_added'      => date($this->language->get('date_format_short'), strtotime($result['date_added'])).' | '.date($this->language->get('time_format'), strtotime($result['date_added'])),
				'view'            => $this->url->link('okupage/oku/info', 'user_token=' . $this->session->data['user_token'] . '&oku_id=' . $result['oku_id'] . $url, true),
				'save'            => HTTP_CATALOG . $result['attachment']
			);
			
		}

		$data['user_token'] = $this->session->data['user_token'];

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

		//Sort asc and desc order
		$data['sort_oku_id'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.oku_id' . $url, true);
		$data['sort_name']   = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.name' . $url, true);
		$data['sort_passport_no'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.passport_no' . $url, true);
		$data['sort_gender'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.gender' . $url, true);
		$data['sort_nationality'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.nationality' . $url, true);
		$data['sort_category'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=category' . $url, true);
		$data['sort_loname'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=ok.lo_name' . $url, true);
		$data['sort_date_added'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_oku_id'])) {
			$url .= '&filter_oku_id=' . $this->request->get['filter_oku_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_passport_no'])) {
			$url .= '&filter_passport_no=' . $this->request->get['filter_passport_no'];
		}

		if (isset($this->request->get['filter_gender'])) {
			$url .= '&filter_gender=' . $this->request->get['filter_gender'];
		}

		if (isset($this->request->get['filter_nationality'])) {
			$url .= '&filter_nationality=' . $this->request->get['filter_nationality'];
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['filter_loname'])) {
			$url .= '&filter_loname=' . $this->request->get['filter_loname'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $oku_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($oku_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($oku_total - $this->config->get('config_limit_admin'))) ? $oku_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $oku_total, ceil($oku_total / $this->config->get('config_limit_admin')));

		$data['filter_oku_id'] = $filter_oku_id;
		$data['filter_name'] = $filter_name;
		$data['filter_passport_no'] = $filter_passport_no;
		$data['filter_gender'] = $filter_gender;
		$data['filter_nationality'] = $filter_nationality;
		$data['filter_category_id'] = $filter_category_id;
		$data['filter_category'] = $filter_category;
		$data['filter_justification'] = $filter_justification;
		$data['filter_loname'] = $filter_loname;
		$data['filter_date_added'] = $filter_date_added;
		$data['sort'] = $sort;
		$data['order'] = $order;

		// Categories
		$this->load->model('catalog/category');
		$data['categories'] = $this->model_okupage_oku->getOkuCategories();

		// API login
		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

		// API login
		$this->load->model('user/api');
		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
			$session = new Session($this->config->get('session_engine'), $this->registry);

			$session->start();
			$this->model_user_api->deleteApiSessionBySessionId($session->getId());
			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			$session->data['api_id'] = $api_info['api_id'];

			$data['api_token'] = $session->getId();
		} else {
			$data['api_token'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		date_default_timezone_set('Asia/Kuala_Lumpur');

		//chekc if user pressed unchecked Print button 
		if(isset($this->request->get['print'])){
			$print = $this->request->get['print'];
		} else {
			$print = null;
		}

		if($print == '1'){
            ini_set('memory_limit', '640M');

			//retrieve list of OKU list without pagination
            $resultPdf = $this->model_okupage_oku->getOkuPdf($filter_data);
			$print_date = date('Y-m-d');
			$selected_array = array();

			if (isset($this->request->get['selected'])) {

				//retrieve all selected rows in table into array
				$url_string = $this->request->get['selected'];
				$url_array = explode('?', $url_string, -1);
				$filter_data['selected_array'] =$url_array;
			}

			//format the file and folder name for PDF file
			$pdf_name = 'okuList_'. date('Ymd') .'_' . uniqid() . '.pdf';
			$upload_folder = 'oku_list/' . $print_date .'/';
			$path_of_uploaded_print = $upload_folder . $pdf_name;
			$path_of_uploaded_print_pdf = DIR_ADMIN . $upload_folder . $pdf_name;
			
			$resultPdf = $this->model_okupage_oku->getOkuPdf($filter_data);
            
            foreach ($resultPdf as $resultsPdf) {
	
				if ($resultsPdf['gender'] == 1 || $resultsPdf['gender'] == '1') {
					$resultsPdf['gender'] = $text_male;
				}else {
					$resultsPdf['gender'] = $text_female;
				}

				$data['oku2'][] = array(
					'oku_id'      	  => $resultsPdf['oku_id'],
					'name'     		  => $resultsPdf['name'],
					'passport_no'     => $resultsPdf['passport_no'],
					'gender'          => $resultsPdf['gender'],
					'nationality'     => $resultsPdf['nationality'],
					'category_id'     => $resultsPdf['category_id'],
					'category'        => $resultsPdf['category'],
					'justification'   => $resultsPdf['justification'],
					'loname'          => $resultsPdf['lo_name'],
					// 'date_added'      => $resultsPdf['date_added'],
					'date_added'      => date($this->language->get('date_format_short'), strtotime($resultsPdf['date_added'])).' | '.date($this->language->get('time_format'), strtotime($resultsPdf['date_added'])),
					'view'            => $this->url->link('okupage/oku/info', 'user_token=' . $this->session->data['user_token'] . '&oku_id=' . $resultsPdf['oku_id'] . $url, true),
				);
			}
	
			//retrieve all text from lang file 
			$titleOkuVerificationForm      = $this->language->get('text_list');
			$column_no                     = $this->language->get('column_no');
			$column_name                   = $this->language->get('column_name');
			$column_passport_no            = $this->language->get('column_passport_no');
			$column_gender                 = $this->language->get('column_gender');
			$column_nationality            = $this->language->get('column_nationality');
			$column_category               = $this->language->get('column_category');
			$column_justification          = $this->language->get('column_justification');
			$column_loname                 = $this->language->get('column_loname');
			$column_date_added             = $this->language->get('column_date_added');
			$text_updated_list             = $this->language->get('text_updated_list');
			$text_total_number_of_entry    = $this->language->get('text_total_number_of_entry');
			$counter = 0;
	
			//pdf layout
			$html = '<div class="row">';
			$html .= '<div class="col-sm-12"><h4 style="text-decoration:underline;">'. $titleOkuVerificationForm .'</h4></div></div>';
			$html .= '<div class="row" style="margin-bottom:20px;">';
			$html .= '<div class="row">';
			$html .= '<div class="col-sm-12">';
			$html .= '<table class="table" style="width:100%; border:1px solid #000; border-collapse: collapse;font-size:12px;">';
			$html .= '<tr style="background:#efefef;border:1px solid #000;">';
			$html .= '<td style="padding:8px; border:1px solid #000;width:7%;"><b> '. $column_no .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;"><b> '. $column_name .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;width:14%;"><b> '. $column_passport_no .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;width:9%;"><b> '. $column_gender .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;width:12%;"><b> '. $column_nationality .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;"><b> '. $column_category .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;width:14%;"><b> '. $column_justification .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;"><b> '. $column_loname .' </b></td>';
			$html .= '<td style="padding:8px; border:1px solid #000;width:12%;"><b> '. $column_date_added .' </b></td>';
			$html .= '</tr>';
			//start column [add loop here]
			foreach ($data['oku2'] as $okus) {
				$counter++;
				$html .= '<tr style="background:#efefef;border:1px solid #000;">';
				$html .= '<td style="padding:8px; border:1px solid #000;width:7%;"> '. $counter .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;"> '. $okus['name'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;width:14%;"> '. $okus['passport_no'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;width:9%;"> '. $okus['gender'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;width:12%;"> '. $okus['nationality'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;"> '. $okus['category'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;width:14%;"> '. $okus['justification'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;"> '. $okus['loname'] .' </td>';
				$html .= '<td style="padding:8px; border:1px solid #000;width:12%;"> '. $okus['date_added'] .' </td>';
				$html .= '</tr>';
				
			}
			$html .= '</table>';
			$html .= '</div>';
			$html .= '</div>';
			$html .= '<div style="width:100%; margin-top:60px;">';
			$html .= '<div style="width:60%;float:left;">';
			$html .= '<h5 style="margin-bottom:15px;">'.  $text_updated_list .'</h5>';
			$html .= '<span>'.  date("Y-m-d H:i:s") .'</span>';
			$html .= '<h5 style="margin-bottom:15px;">'. $text_total_number_of_entry .'</h5>';
			$html .= '<span>' . $counter .'</span>';
			$html .= '</div>';
			$html .= '<div style="width:40%;float:right; display:block;margin-left:200px;">';
			$html .= '</div>';
			$html .= '</div>';


            // echo $html;exit;
            $this->load->library('pdf');

            $pdf_name = 'okuList_'. date('Ymd') .'_' . uniqid() . '.pdf';
			$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
			
			$this->pdf->data['html'] = $html;
			$this->pdf->Build();
			$this->pdf->Draw();
			ob_end_clean();
			$this->pdf->tcpdf->Output($path_of_uploaded_print_pdf, 'I');

        }
		$this->response->setOutput($this->load->view('okupage/oku', $data));
	}
	
	public function info() {

		$this->load->model('okupage/oku');
		$this->load->language('okupage/oku');
		$filter_data = array();
		$oku_info = array();

		if (isset($this->request->get['oku_id'])) {
			$oku_id = $this->request->get['oku_id'];
		} else {
			$oku_id = 0;
		}

		$oku_info = $this->model_okupage_oku->getOneOku($oku_id);
		$text_male = $this->language->get('text_male');
		$text_female = $this->language->get('text_female');

		foreach ($oku_info as $oku_infos) {

			//convert gender_id into a text string
			if ($oku_infos['gender'] == 1 || $oku_infos['gender'] == '1') {
				$oku_infos['gender'] = $text_male;
			}else {
				$oku_infos['gender'] = $text_female;
			}
			$data['oku'][] = array(
				'oku_id'      	  => $oku_infos['oku_id'],
				'name'     		  => $oku_infos['name'],
				'passport_no'     => $oku_infos['passport_no'],
				'gender'          => $oku_infos['gender'],
				'nationality'     => $oku_infos['nationality'],
				'category_id'     => $oku_infos['category_id'],
				'category'        => $oku_infos['category'],
				'justification'   => $oku_infos['justification'],
				'loname'          => $oku_infos['lo_name'],
				'date_added'      => date($this->language->get('date_format_short'), strtotime($oku_infos['date_added'])).' | '.date($this->language->get('time_format'), strtotime($oku_infos['date_added'])),
				'save'            => HTTP_CATALOG . $oku_infos['attachment']
			);

			// echo "<pre>"; 
			// print_r($data['oku']);
			// echo "</pre>"; 
			
		}

		if ($oku_info) {
			$this->load->language('okupage/oku');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			$data['text_oku'] = sprintf($this->language->get('text_oku'), $this->request->get['oku_id']);

			$url = '';

			if (isset($this->request->get['filter_oku_id'])) {
                $url .= '&filter_oku_id=' . $this->request->get['filter_oku_id'];
            }

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_passport_no'])) {
                $url .= '&filter_passport_no=' . $this->request->get['filter_passport_no'];
            }

            if (isset($this->request->get['filter_gender'])) {
                $url .= '&filter_gender=' . $this->request->get['filter_gender'];
            }

            if (isset($this->request->get['filter_nationality'])) {
                $url .= '&filter_nationality=' . $this->request->get['filter_nationality'];
            }

            if (isset($this->request->get['filter_loname'])) {
                $url .= '&filter_loname=' . $this->request->get['filter_loname'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

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
				'href' => $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			//the three buttons on header
			$data['edit'] = $this->url->link('okupage/oku/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['oku_id'], true);
			$data['cancel'] = $this->url->link('okupage/oku', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['user_token'] = $this->session->data['user_token'];
			$data['oku_id'] = (int)$this->request->get['oku_id'];

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($oku_info[0]['date_added']));

			// The URL we send API requests to
			$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

			// API login
			$this->load->model('user/api');

			$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

			if ($api_info && $this->user->hasPermission('modify', 'sale/order')) {
				$session = new Session($this->config->get('session_engine'), $this->registry);

				$session->start();

				$this->model_user_api->deleteApiSessionBySessionId($session->getId());

				$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);

				$session->data['api_id'] = $api_info['api_id'];

				$data['api_token'] = $session->getId();
			} else {
				$data['api_token'] = '';
			}

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('okupage/oku_info', $data));
		} else {
			return new Action('error/not_found');
		}

	}

}