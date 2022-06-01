<?php
class ControllerCommonHometwo extends Controller {
	public function index() {

		$this->load->language('okupage/oku1');
		$this->load->model('okupage/oku1');

		if(isset($this->session->data['success'])){
			$data['success_message'] = $this->language->get('text_success');
			unset($this->session->data['success']);
		}
		//laod dompdf
		$this->load->library('pdf');

		//laod email driver
		//$this->load->library('email_cron');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['okucat_info'] = $this->model_okupage_oku1->getOkuCategory();

		//send these $data to view
		$this->response->setOutput($this->load->view('okupage/oku2', $data));

		//need to fix this, its not working
		date_default_timezone_set('Asia/Kuala_Lumpur');

    if(isset($this->request->post['submit'])) {

       $error = 0;

       if(empty($this->request->post['gender'])){
            $error_gender = 'Please select gender';
            $error = 1;
       }

       if(empty($this->request->post['category_oku'])){
            $error_category = 'Please select category';
            $error = 1;
       }


       if($error == 0){

            if(isset($_FILES['photo'])){
                if(!empty($_FILES['photo']['name'])){
                    $uploaded_file = $_FILES['photo'];

                    $name_of_uploaded_file =  basename($uploaded_file['name']);

					$name = explode('.', $name_of_uploaded_file);

					$new_name = 'okuform_'. date('Ymd') .'_' . uniqid() . '.' . $name[1];

                    $date = date('Y-m-d');

                    if (!file_exists('oku_images/' . $date)) {
                        mkdir('oku_images/' .  $date, 0777, true);
                    }

                    if (!file_exists('oku_images/' .  $date .'/' )) {
                        mkdir('oku_images/' .  $date .'/', 0777, true);
                    }

                    $upload_folder = 'oku_images/' . $date .'/';

                    //copy the temp. uploaded file to uploads folder
                    $path_of_uploaded_file_image = $upload_folder . $new_name;

					$this->compressImage($uploaded_file["tmp_name"],$path_of_uploaded_file_image,60);

                    $image_path = $path_of_uploaded_file_image;
                }
            }


			$oku_array = array(
				'name' 			    	=> $this->request->post['name'],
				'passport_no'	        => $this->request->post['passport_no'],
				'gender'                => $this->request->post['gender'],
				'nationality'           => $this->request->post['nationality'],
				'category_oku'          => $this->request->post['category_oku'],
				'justification'         => $this->request->post['justification'],
				'lo_name'               => $this->request->post['lo_name']
			);

			$fileName = 'okuform_'. date('Ymd') .'_'. uniqid() .".pdf";

			if (!file_exists(DIR_OKU_FORM_PDF . $oku_array['name'])) {
				mkdir(DIR_OKU_FORM_PDF. $oku_array['name'], 0777, true);
			}

			$upload_folder_short = 'oku_form_pdf/' . $oku_array['name'] . '/';
			$upload_folder_pdf = DIR_OKU_FORM_PDF . $oku_array['name'] . '/';


			$path_of_uploaded_file_pdf = $upload_folder_pdf . $fileName;
			$path_of_uploaded_file_short = $upload_folder_short . $fileName;

			$oku_array['attachment'] = $path_of_uploaded_file_short;
			$oku_array['image_path'] = $image_path;

			//enter data into model function
			$this->model_okupage_oku1->setOkuVerificationForm($oku_array);

			$okucategory = $this->model_okupage_oku1->getOkuCategory();
			$oku_array_index = $oku_array['category_oku'];

			$category_name = $this->model_okupage_oku1->getOneOkuCategory($oku_array['category_oku']);

			$titleOkuVerificationForm = $this->language->get('heading_title');

			$gender='';
			//change genderID to string
			if($oku_array['gender'] == '1' or $oku_array['gender'] == 1){
					$gender = 'Male';
			}else{
					$gender = 'Female';
			}

            // Load HTML content
            $html = '<link rel="stylesheet" href="catalog/view/theme/default/template/okupage/css/bootstrap.min.css"><script src="catalog/view/theme/default/template/okupage/js/bootstrap.min.js"></script>';
            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12"><h4 style="text-decoration:underline;">'. $titleOkuVerificationForm .'</h4></div></div>';
            $html .= '<div class="row" style="margin-bottom:20px;">';
            $html .= '<div class="col-sm-12"><div style="text-align:center;margin-bottom:10px;"><img src="'. $image_path .'" alt="Photo attachment" height="75px"></div></div></div>';
            $html .= '<div class="row">';
            $html .= '<div class="col-sm-12">';
            $html .= '<table class="table" style="width:100%; border:1px solid #000; border-collapse: collapse;font-size:12px;">';
            $html .= '<tr style="background:#efefef;border:1px solid #000;">';
            $html .= '<td style="padding:8px; border:1px solid #000;width:40%;"><b> Name </b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['name'] .'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>Passport No (Non-Malaysian)</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['passport_no'].'</td>';
            $html .= '</tr>';
            $html .= '<tr style="background:#efefef;">';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>Gender</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'.$gender.'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>Nationality</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['nationality'].'</td>';
            $html .= '</tr>';

			foreach($category_name as $category_names){
				$html .= '<tr style="background:#efefef;">';
				$html .= '<td style="padding:8px; border:1px solid #000;"><b>Category OKU</b></td>';
				$html .= '<td style="padding:8px; border:1px solid #000;">'.  $category_names['category'] . '</td>';
				$html .= '</tr>';
			}

            $html .= '<tr>';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>Justification</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['justification'] .'</td>';
            $html .= '</tr>';
            $html .= '<tr style="background:#efefef;">';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>LO Name</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['lo_name'].'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="padding:8px; border:1px solid #000;"><b>Date</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;" >'. date('d-m-Y').'</td>';
            $html .= '</tr>';
            $html .= '<tr style="background:#efefef;">';
            $html .= '<td style="padding:8px; border:1px solid #000; "><b>Time</b></td>';
            $html .= '<td style="padding:8px; border:1px solid #000;">'. date('h:i:s A').'</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div style="width:100%; margin-top:60px;">';
            $html .= '<div style="width:60%;float:left;">';
            $html .= '<h5 style="margin-bottom:100px;">Ticket Generated By:</h5>';
            $html .= '<span>___________________________</span>';
            $html .= '</div>';
            $html .= '<div style="width:40%;float:right; display:block;margin-left:200px;">';
            $html .= '<h5 style="margin-bottom:100px;">Approved By:</h5>';
            $html .= '<span style="display:block;">___________________________</span><br/>';
            $html .= '<span style="font-weight:bold;">('. $oku_array['lo_name'].')</span>';
            $html .= '</div>';
            $html .= '</div>';

			// // Output the generated PDF to Browser
			//echo $html;exit;

			$this->pdf->data['html'] = $html;
			$this->pdf->Build();
			$this->pdf->Draw();
			$this->pdf->tcpdf->Output($path_of_uploaded_file_pdf, 'F');

			$email_body = '<div style="width:600px;"><img src="cid:oku_image" width="400"/></div><br/><br/>';
			$email_body .= '<table style="width:600px; border:1px solid #000; border-collapse: collapse;font-size:12px;">';
			$email_body .= '<tr style="background:#efefef;border:1px solid #000;">';
			$email_body .= '<td style="padding:8px; border:1px solid #000;width:40%;"><b>Name</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['name'].'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Passport No (Non-Malaysian)</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['passport_no'].'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr style="background:#efefef;">';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Gender</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $gender.'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Nationality</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['nationality'].'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr style="background:#efefef;">';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Category OKU</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $category_name.'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Justification</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['justification'].'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr style="background:#efefef;">';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>LO Name</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. $oku_array['lo_name'].'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;"><b>Date</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;" >'. date('d-m-Y').'</td>';
			$email_body .= '</tr>';
			$email_body .= '<tr style="background:#efefef;">';
			$email_body .= '<td style="padding:8px; border:1px solid #000; "><b>Time</b></td>';
			$email_body .= '<td style="padding:8px; border:1px solid #000;">'. date('h:i:s A').'</td>';
			$email_body .= '</tr>';
			$email_body .= '</table>';

			$to = @serialize($this->customer->getEmail());
			$from = $this->language->get('text_email_from');
			//$from = "";
			$subject = $this->language->get('text_email_subject');

			//Output the generated email table content to Browser
			// echo "<pre>";
			// print_r ($email_body);
			// echo "</pre>";
			// exit;
			$this->model_okupage_oku1->sendEmail($to, $from,$subject,$html, $path_of_uploaded_file_short, $fileName);

		}
		$this->session->data['success']  = 1;
		$this->response->redirect($this->url->link('common/home', '', true));
    }

	}

	// Compress image
	public function compressImage($source, $destination, $quality) {

		$info = getimagesize($source);

		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($source);
		elseif ($info['mime'] == 'image/gif')
			$image = imagecreatefromgif($source);
		elseif ($info['mime'] == 'image/png')
			$image = imagecreatefrompng($source);

		imagejpeg($image, $destination, $quality);
	}


}
