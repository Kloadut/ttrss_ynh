<?php
class Af_Comics_Dilbert extends Af_ComicFilter {

	function supported() {
		return array("Dilbert");
	}

	function process(&$article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["guid"], "dilbert.com") !== FALSE) {
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));

				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query('(//img[@src])'); // we might also check for img[@class='strip'] I guess...

					$matches = array();

					foreach ($entries as $entry) {

						if (preg_match("/dyn\/str_strip\/.*zoom\.gif$/", $entry->getAttribute("src"), $matches)) {

							$entry->setAttribute("src",
								rewrite_relative_url("http://dilbert.com/",
								$matches[0]));

							$basenode = $entry;
							break;
						}
					}

					if ($basenode) {
						$article["content"] = $doc->saveXML($basenode);
					}
				}

			return true;
		}

		return false;
	}
}
?>
