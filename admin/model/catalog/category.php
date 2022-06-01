<?php
class ModelCatalogCategory extends Model {
	public function addCategory($data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "okucategory SET category_id = '" . $data['category_id'] . "', category = '" . $this->db->escape(strip_tags($data['category'])) . "', category_status = '" . (int)$data['category_status'] . "'");
		$this->cache->delete('category');
		return $category_id;

	}

	public function editCategory($category_id, $data) {
		$query = $this->db->query("SELECT `category` FROM " . DB_PREFIX . "okucategory WHERE category_id = '" . (int)$category_id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "okucategory SET category = '" . $this->db->escape($data['category']) . "',category_status = '" . (int)$data['category_status'] . "' WHERE category_id = '" . (int)$category_id . "'");
		$this->cache->delete('category');
	}

	public function deleteCategory($category_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "okucategory WHERE category_id = '" . (int)$category_id ."'");

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "okucategory WHERE category_id = '" . (int)$category_id ."'");

		foreach ($query->rows as $result) {
			$this->deleteCategory($result['category_id']);
		}
		$this->cache->delete('category');
	}

	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "okucategory WHERE category_id = '" . (int)$category_id . "'");
		return $query->row;
	}

	public function getCategories($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "okucategory ";
		$sql .= " GROUP BY category_id";

		$sort_data = array(
			'category',
			'category_status',
			'sort_order'
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCategories() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "okucategory");

		return $query->row['total'];
	}

}
