<?php
class ModelOkupageOku1 extends Model {

	public function getOkuCategory() {
		$okuCategory_data = array();

		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."okucategory` WHERE category_status ='1'");

		foreach ($query->rows as $result) {
			$okuCategory_data[] = array(
				'category_id'   => $result['category_id'],
				'category'         => $result['category']
			);
		}
		return $okuCategory_data;
	}

	public function getOneOkuCategory($category_id) {

		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."okucategory` WHERE category_status ='1' AND category_id = '". $category_id."' ");
		return $query->rows;
	}

	public function setOkuVerificationForm($data = array()) {


		$this->db->query("INSERT INTO `" . DB_PREFIX . "oku` set `name`  = '". $data['name']."', `passport_no`  = '". $data['passport_no']."', gender = '". (int)$data['gender']."', nationality = '". $data['nationality']."', category_id = '". (int)$data['category_oku']."',  justification = '". $data['justification']."', image_path = '". $data['image_path'] ."', lo_name = '". $data['lo_name']."', date_added = '". date("Y-m-d H:i:s")."' , attachment = '". $data['attachment']."'        ");

	}

	public function sendEmail($to, $from,$subject,$html,$attachment, $attachment_name){

			$this->db->query("INSERT INTO email_cron SET mail_from='".$from."', mail_to='".$to."', subject='".$subject."', body='".$html."', attachment='".$attachment."', attachment_name='".$attachment_name."', date_added = '". date("Y-m-d H:i:s")."', system='Funicular Pass System'");

	}

	


}
