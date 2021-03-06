<?php


class Dns {

	private $email;
	private $auth_key;
	private $zone;


	public function __construct() {
		$config = json_decode(file_get_contents(dirname(__FILE__) . '/config.json'), true);
		$this->email = $config['email'];
		$this->auth_key = $config['token'];
		$this->zone = $config['zone'];
	}

	//set Functions
	public function setAuthKey($token) {
		$this->auth_key = $token;
	}
	public function setEmail($mail) {
		$this->email = $mail;
	}
	public function setZone($zone) {
		$this->zone = $zone;
	}

	//DNS functions
	public function create(array $data = null) {
		return $this->request($data, 'post');
	}

	public function info($data) {
		return $this->request($data, 'get');
	}

	public function update(array $data = null, $id) {
		return $this->request($data, 'put');
	}

	public function delete($id)
	{
		return $this->request($id, 'delete');
	}

	//global request function
	public function request($data, $method) {
		if (isset($this->email) && isset($this->auth_key)) {

			$url = 'https://api.cloudflare.com/client/v4/zones/' . $this->zone . '/dns_records';
			$headers = array("X-Auth-Email: {$this->email}", "X-Auth-Key: {$this->auth_key}");

			$curl_options = array(
				CURLOPT_VERBOSE        => false,
				CURLOPT_FORBID_REUSE   => true,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER         => false,
				CURLOPT_TIMEOUT        => 5,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_FOLLOWLOCATION => true
			);

			$ch = curl_init();
			curl_setopt_array($ch, $curl_options);

			if($method === 'post') {
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			} else if ($method === 'put') {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			} else if ($method === 'delete') {
				$url .= '/' . $data;
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				$headers[] = "Content-type: application/json";
			} else {
				$url .= '?name=' . $data;
			}

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_URL, $url);
			$http_result = curl_exec($ch);
			curl_close($ch);
			return json_decode($http_result);

		} else {
			echo 'Email or Auth key not set!';
		} 
	}
}
?>
