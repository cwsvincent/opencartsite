<?php
class ModelOkupageOku extends Model {

	public function getOneOku($oku_id) {

		//sql strign to query for one oku entry
    	$sql = "SELECT *, (SELECT c.category FROM oc_okucategory c WHERE c.category_id = ok.category_id) AS category FROM oc_oku ok WHERE ok.oku_id = $oku_id;";
		
		//query the database
		$query = $this->db->query($sql);
		return $query->rows;
	}

  	public function getOku($data = array()) {
		
		//sql strign to query for one oku entry
   		$sql = "SELECT *, (SELECT c.category FROM " . DB_PREFIX . "okucategory c WHERE c.category_id = ok.category_id) AS category FROM " . DB_PREFIX . "oku ok";
	
		if (!empty($data['filter_category'])) {
			$implode = array();
			$categories = explode(',', $data['filter_category']);

			foreach ($categories as $category_id) {
				$implode[] = "ok.category_id = '" . (int)$category_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}

			//if category_id is inputed and the category_id is not blank
			} elseif (isset($data['filter_category_id']) && $data['filter_category_id'] !== '') {
			$sql .= " WHERE ok.category_id = '" . (int)$data['filter_category_id'] . "'";
			}else {
				$sql .= " WHERE ok.category_id > '0'";
			}

			if (!empty($data['filter_oku_id'])) {
				$sql .= " AND oku_id = '" . (int)$data['filter_oku_id'] . "'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}

			if (!empty($data['filter_passport_no'])) {
					$sql .= " AND passport_no = '" . (int)$data['filter_passport_no'] . "'";
				}

			if (isset($data['filter_gender']) && $data['filter_gender'] !== '') {
				$sql .= " AND gender = '" . (int)$data['filter_gender'] . "'";
				}

			if (!empty($data['filter_nationality'])) {
					$sql .= " AND nationality LIKE '%" . $this->db->escape($data['filter_nationality']) . "%'";
				}

			if (!empty($data['filter_category_id'])) {
					$sql .= " AND category_id = '" . (int)$data['filter_category_id'] . "'";
				}


			if (!empty($data['filter_justification'])) {
					$sql .= " AND justification LIKE '%" . $this->db->escape($data['filter_justification']) . "%'";
				}

			if (!empty($data['filter_loname'])) {
					$sql .= " AND lo_name LIKE '%" . $this->db->escape($data['filter_loname']) . "%'";
				}

			if (!empty($data['filter_date_added'])) {
				$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			}


			if (!empty($data['filter_total'])) {
				$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
			}


			$sort_data = array(
				'ok.oku_id',
				'ok.name',
				'ok.passport_no',
				'ok.gender',
				'ok.nationality',
				'ok.category_id',
				'category',
				'ok.lo_name',
				'ok.date_added',
			);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		//query the database
		$query = $this->db->query($sql);
		return $query->rows;
	}


	public function getOkuPdf($data = array()) {
		
		//sql strign to query for one oku entry
   		$sql = "SELECT *, (SELECT c.category FROM " . DB_PREFIX . "okucategory c WHERE c.category_id = ok.category_id) AS category FROM " . DB_PREFIX . "oku ok";

		//check for filter_data
		if (!empty($data['filter_category'])) {
			$implode = array();

			$categories = explode(',', $data['filter_category']);

			foreach ($categories as $category_id) {
				$implode[] = "ok.category_id = '" . (int)$category_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
			//if category_id is inputed and the category_id is not blank
		} elseif (isset($data['filter_category_id']) && $data['filter_category_id'] !== '') {
		$sql .= " WHERE ok.category_id = '" . (int)$data['filter_category_id'] . "'";
		}else {
			$sql .= " WHERE ok.category_id > '0'";
		}

		if (!empty($data['filter_name'])) {
		$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_passport_no'])) {
				$sql .= " AND passport_no = '" . (int)$data['filter_passport_no'] . "'";
			}

		if (isset($data['filter_gender']) && $data['filter_gender'] !== '') {
			$sql .= " AND gender = '" . (int)$data['filter_gender'] . "'";
			}

		if (!empty($data['filter_nationality'])) {
				$sql .= " AND nationality LIKE '%" . $this->db->escape($data['filter_nationality']) . "%'";
			}

		if (!empty($data['filter_category_id'])) {
				$sql .= " AND category_id = '" . (int)$data['filter_category_id'] . "'";
			}


		if (!empty($data['filter_justification'])) {
				$sql .= " AND justification LIKE '%" . $this->db->escape($data['filter_justification']) . "%'";
			}

		if (!empty($data['filter_loname'])) {
				$sql .= " AND lo_name LIKE '%" . $this->db->escape($data['filter_loname']) . "%'";
			}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}


		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		
		if (!empty($data['selected_array'])) {

			$sql .=  " AND ";
			$array_counter = 0;

			foreach($data['selected_array'] as $selected_oku_id){
				$array_counter ++;
				
				$sql .=  " oku_id = '" . (int)$selected_oku_id . "'  ";
				
				if ($array_counter != sizeof($data['selected_array'])){
					$sql .=  " OR ";
				}
			}
		}

		$sort_data = array(
			'ok.oku_id',
			'ok.name',
			'ok.passport_no',
			'ok.gender',
			'ok.nationality',
			'ok.category_id',
			'category',
			'ok.lo_name',
			'ok.date_added',
		);

    	if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ok.oku_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		//query the database
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getTotalOku($data = array()) {

		//sql strign to query for one oku entry
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "oku`";
		
		//check for filter_data
		if (!empty($data['filter_category'])) {
		$implode = array();

		$categories = explode(',', $data['filter_category']);

		foreach ($categories as $category_id) {
			$implode[] = "category_id = '" . (int)$category_id . "'";
		}

		if ($implode) {
			$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
		}
		} elseif (isset($data['filter_category_id']) && $data['filter_category_id'] !== '') {
				$sql .= " WHERE category_id = '" . (int)$data['filter_category_id'] . "'";
		} else {
		$sql .= " WHERE category_id > '0'";
		}

		if (!empty($data['filter_oku_id'])) {
			$sql .= " AND oku_id = '" . (int)$data['filter_oku_id'] . "'";
		}
	
		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
	
		if (!empty($data['filter_passport_no'])) {
				$sql .= " AND passport_no = '" . (int)$data['filter_passport_no'] . "'";
		}
	
		if (isset($data['filter_gender']) && $data['filter_gender'] !== '') {
			$sql .= " AND gender = '" . (int)$data['filter_gender'] . "'";
		}
	
		if (!empty($data['filter_nationality'])) {
			$sql .= " AND nationality LIKE '%" . $this->db->escape($data['filter_nationality']) . "%'";
		}
	
		if (!empty($data['filter_category_id'])) {
			$sql .= " AND category_id = '" . (int)$data['filter_category_id'] . "'";
		}
		
		if (!empty($data['filter_justification'])) {
			$sql .= " AND justification LIKE '%" . $this->db->escape($data['filter_justification']) . "%'";
		}
		
		if (!empty($data['filter_loname'])) {
			$sql .= " AND lo_name LIKE '%" . $this->db->escape($data['filter_loname']) . "%'";
		}
		
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		//query the database
		$query = $this->db->query($sql);
		return $query->row['total'];

	}

	

	public function getOkuCategories($data = array()) {
		if($data){
		$sql = "SELECT *  FROM " . DB_PREFIX . "okucategory ";
		$sql .= "ORDER BY category_id";


		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	    }else{
		$category_data = $this->cache->get('category');

			if (!$category_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "okucategory");

				$category_data = $query->rows;

				$this->cache->set('category', $category_data);
	 		}

		}
	 return $category_data;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "okucategory");

		return $query->row['total'];
	}

	public function getCategoryDescriptions($category_id) {
		$category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "okucategory WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_data[$result] = array('category' => $result['category']);
		}

		return $category_data;
	}




}
