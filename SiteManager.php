<?php
include 'dns.php';
include 'vhost.php';


class SiteManager {

	private $zone;
	private $cname;
	private $data;
	private $id;

	public function __construct() {
		$this->dns = new Dns();
	}

	public function setZone($zone) {
		$this->dns->zone = $zone;
	}

	public function setSite($site) {
		$this->cname = strtolower($site);
		$this->vhost = new Vhost($this->cname);
		$this->data = array(
			'type'    => 'CNAME',
			'name'    => $this->cname,
			'content' => 'domain.com',
			'ttl'     => 120
		);
	}

	public function getSite() {
		return $this->cname;
	}

	public function getAllSites() {
		return array_diff(scandir('/etc/apache2/sites-available/'), array('..', '.'));
	}

	public function getId() {
		$this->id = $this->dns->info($this->cname . '.domain.com')->result[0]->id;
	}

	//Create new Site
	public function create() {

		if ($this->dns->create($this->data)->success) {
			$this->vhost->setup();
			return true;
		} else {
			return false;
		}
	}

	//Delete Site
	public function delete() {
		$this->getId();
		$this->dns->delete($this->id);
		$this->vhost->delete();
	}
}
?>
