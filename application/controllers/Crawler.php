<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crawler extends CI_Controller {
	public function cars()
	{
		$query = $this->db->query("SELECT * FROM fresh WHERE `link` LIKE '%oferta%' ORDER BY id DESC LIMIT 1");
		$result = $query->result_array();

		// remove when getted
		/*
			DELETE FROM posts WHERE id IN (
			    SELECT * FROM (
			        SELECT id FROM posts GROUP BY id HAVING ( COUNT(id) > 1 )
			    ) AS p
			)
		*/

		$crawler = new Crawler\OLX_Cars_Core($result[0]['link'], $this->db, "Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0", 1, []); 
		$crawler->setLimitStart(microtime(true));

		foreach($result as $r) {
			//var_dump($r['link']);
			$crawler->crawl($r['link'], 1);
		}

		// init crawler
		//$crawler = new Crawler\Core($result['link'], $this->db, "Mozilla", 5, $filters);
		//$crawler->init();

		//$crawler = new Crawler\OLX_Cars_Core($result['link'], $this->db, "Mozilla", 5, $filters);
		//$crawler->init();
		//var_dump($crawler->getVisited());

		// cleanup links after all
		//$this->db->query("DELETE FROM `fresh` WHERE `link` NOT LIKE '%oferta%' ");
	}

	public function index() {
		// "https://www.olx.pl/motoryzacja/samochody/"
		$query = $this->db->query("SELECT * FROM fresh ORDER BY id DESC LIMIT 1");
		$result = $query->row_array();

		// prevent from view again
		$this->db->query("DELETE FROM fresh WHERE id = ?", [$result['id']]);

		echo 'Crawling start at: ' . $result['link'];

		$filters = [
			"pomoc.olx.pl",
			"tu-dodasz-reklame.olx.pl"
		];

		// init crawler
		$crawler = new Crawler\Core($result['link'], $this->db, "Mozilla", 5, $filters);
		$crawler->init();

		// cleanup links after all
		$this->db->query("DELETE FROM `fresh` WHERE `link` NOT LIKE '%oferta%' ");

		//var_dump($crawler->getVisited());

		//$response = $crawler->getContent();
		//var_dump($response);
	}
}