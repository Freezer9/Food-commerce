<?php
session_start();
class Action
{
	private $db;

	public function __construct()
	{
		ob_start();
		include 'db_connect.php';

		$this->db = $conn;
	}

	function __destruct()
	{
		pg_close($this->db);
		ob_end_flush();
	}

	function login()
	{
		extract($_POST);
		$query = "SELECT * FROM users where username = '" . $username . "' and password = '" . $password . "' ";
		$qry = pg_query($this->db, $query);
		if (pg_num_rows($qry) > 0) {
			foreach (pg_fetch_array($qry) as $key => $value) {
				if ($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 3;
		}
	}
	function login2()
	{
		extract($_POST);
		$query = "SELECT * FROM user_info where email = '" . $email . "' and password = '" . md5($password) . "' ";
		$qry = pg_query($this->db, $query);
		if (pg_num_rows($qry) > 0) {
			foreach (pg_fetch_array($qry) as $key => $value) {
				if ($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
			$query = "UPDATE cart set user_id = '" . $_SESSION['login_user_id'] . "' where client_ip ='$ip' ";
			$qry = pg_query($this->db, $query);
			return 1;
		} else {
			return 3;
		}
	}
	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user()
	{
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if (empty($id)) {
			$query = "INSERT INTO users (name, username, password, type) VALUES ('$name', '$username', '$password', '$type')";
			$save = pg_query($this->db, $query);
		} else {
			$query = "UPDATE users set " . $data . " where id = " . $id;
			$save = pg_query($this->db, $query);
		}
		if ($save) {
			return 1;
		}
	}
	function signup()
	{
		extract($_POST);
		$hashedPassword = md5($password);
		$chk = pg_num_rows(pg_query($this->db, "SELECT * FROM user_info where email = '$email' "));
		if ($chk > 0) {
			return 2;
			exit;
		}
		$query = "INSERT INTO user_info (first_name, last_name, mobile, address, email, password) VALUES ('$first_name', '$last_name', '$mobile', '$address' , '$email', '$hashedPassword')";
		$save = pg_query($this->db, $query);
		if ($save) {
			$login = $this->login2();
			return 1;
		}
	}

	function save_settings()
	{
		extract($_POST);
		$aboutContent = htmlentities(str_replace("'", "&#x2019;", $about));
		if ($_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/' . $fname);
		}

		$query = "SELECT * FROM system_settings";
		$chk = pg_query($this->db, $query);
		if (pg_num_rows($chk) > 0) {
			$query = "UPDATE system_settings SET name = '$name', email = '$email', contact = '$contact', about_content = '$aboutContent'  where id =" . pg_fetch_assoc($chk)['id'];
			$save = pg_query($this->db, $query);
		} else {
			$query = "INSERT INTO system_settings (name, email, contact, about_content, cover_img) VALUES ('$name', '$email', '$contact', '$aboutContent', '$fname')";
			$save = pg_query($this->db, $query);
		}
		if ($save) {
			$query = pg_fetch_assoc(pg_query($this->db, "SELECT * FROM system_settings LIMIT 1"));
			foreach ($query as $key => $value) {
				if (!is_numeric($key))
					$_SESSION['setting_' . $key] = $value;
			}

			return 1;
		}
	}


	function save_category()
	{
		extract($_POST);
		$data = " name = '$name' ";
		if (empty($id)) {
			$query = "INSERT INTO category_list (name) VALUES ('$name')";
			$save = pg_query($this->db, $query);
		} else {
			$query = "UPDATE category_list set " . $data . " where id=" . $id;
			$save = pg_query($this->db, $query);
		}
		if ($save)
			return 1;
	}
	function delete_category()
	{
		extract($_POST);
		$query = "DELETE FROM category_list where id = " . $id;
		$delete = pg_query($this->db, $query);
		if ($delete)
			return 1;
	}
	function save_menu()
	{
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", price = '$price' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", description = '$description' ";
		if (isset($status) && $status  == 'on') {
			$theStatus = 1;
		} else {
			$theStatus = 0;
		}

		if ($_FILES['img']['tmp_name'] != '') {
			$fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/' . $fname);
			$data .= ", img_path = '$fname' ";
		}
		if (empty($id)) {
			$query = "INSERT INTO product_list (name, price, category_id, description, status, img_path)  VALUES ('$name', '$price', '$category_id', '$description', $theStatus, '$fname')";
			$save = pg_query($this->db, $query);
		} else {
			$query = "UPDATE product_list set " . $data . " where id=" . $id;
			$save = pg_query($this->db, $query);
		}
		if ($save)
			return 1;
	}

	function delete_menu()
	{
		extract($_POST);
		$query = "DELETE FROM product_list where id = " . $id;
		$delete = pg_query($this->db, $query);
		if ($delete)
			return 1;
	}

	function add_to_cart()
	{
		extract($_POST);
		$qty = isset($qty) ? $qty : 1;
		if (isset($_SESSION['login_user_id'])) {
			$data .= ", user_id = '" . $_SESSION['login_user_id'] . "' ";
		} else {
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
		}

		$userId = isset($_SESSION['login_user_id']) ? $_SESSION['login_user_id'] : 0;
		$query = "INSERT INTO cart (client_ip, user_id, product_id, qty) VALUES ('$ip', '$userId', $pid, $qty)";
		$save = pg_query($this->db, $query);
		if ($save)
			return 1;
	}
	function get_cart_count()
	{
		extract($_POST);
		if (isset($_SESSION['login_user_id'])) {
			$where = " where user_id = '" . $_SESSION['login_user_id'] . "'  ";
		} else {
			$ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
			$where = " where client_ip = '$ip'  ";
		}
		$query = "SELECT sum(qty) as cart FROM cart " . $where;
		$get = pg_query($this->db, $query);
		if (pg_num_rows($get) > 0) {
			return pg_fetch_array($get)['cart'];
		} else {
			return '0';
		}
	}

	function update_cart_qty()
	{
		extract($_POST);
		$data = " qty = $qty ";
		if ($qty == 0) {
			$query = "DELETE FROM cart WHERE id = $id";
			$save = pg_query($this->db, $query);
		} else {
			$query = "UPDATE cart set " . $data . " where id = " . $id;
			$save = pg_query($this->db, $query);
		}
		if ($save)
			return 1;
	}

	function save_order()
	{
		extract($_POST);
		$name = $first_name . " " . $last_name;
		$query = "INSERT INTO orders (name, address, mobile, email) VALUES ('$name', '$address', '$mobile', '$email') RETURNING id";
		$save = pg_query($this->db, $query);
		if ($save) {
			$id = pg_fetch_array($save)['id'];
			$query = "SELECT * FROM cart where user_id =" . $_SESSION['login_user_id'];
			$qry = pg_query($this->db, $query);
			while ($row = pg_fetch_assoc($qry)) {
				$productId = $row['product_id'];
				$qty = $row['qty'];
				$query = "INSERT INTO order_list (order_id, product_id, qty) VALUES ($id, $productId, $qty)";
				$save2 = pg_query($this->db, $query);
				if ($save2) {
					$query = "DELETE FROM cart where id= $id";
					pg_query($this->db, $query);
				}
			}
			return 1;
		}
	}
	function confirm_order()
	{
		extract($_POST);
		$query = "UPDATE orders SET status = 1 where id= " . $id;
		$save = pg_query($this->db, $query);
		if ($save)
			return 1;
	}
}
