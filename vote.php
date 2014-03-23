<?php

require_once 'includes/common.php';
require_once 'libAllure/util/shortcuts.php';

use \libAllure\Session;
use \libAllure\DatabaseFactory;

function outputJson($o) {
	header('Content-Type: application/json');
	echo json_encode($o);
	exit;
}

$cause = "";

try {
	$dir = san()->filterString('dir');
	$id = san()->filterUint('id');

	switch ($dir) {
		case 'up': $delta = 1; break;
		case 'down': $delta = -1; break;
		default: throw new Exception('What direction is that?!');
	}

	if (!Session::isLoggedIn()) {
		$cause = "needsLogin";
		throw new Exception("You need to be logged in.");
	} else {
		$sql = 'INSERT INTO votes (quote, user, delta) VALUES (:quote, :user, :delta1) ON DUPLICATE KEY UPDATE delta = :delta2 ';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue('quote', $id);
		$stmt->bindValue('user', Session::getUser()->getId());
		$stmt->bindValue('delta1', $delta);
		$stmt->bindValue('delta2', $delta);
		$stmt->execute();

		$sql = 'SELECT SUM(v.delta) AS newVal FROM quotes q LEFT JOIN votes v ON v.quote = q.id WHERE q.id = :id GROUP BY q.id';
		$stmt = DatabaseFactory::getInstance()->prepare($sql);
		$stmt->bindValue('id', $id);
		$stmt->execute();

		$quote = $stmt->fetchRowNotNull();
		$newVal = $quote['newVal'];
	}

	outputJson(array(
		"type" => "ok",
		"id" => $id,
		"newVal" => $newVal,
	));
} catch (Exception $e) {
	$error = array(
		"type" => "error",
		"message" => $e->getMessage(),
		"cause" => $cause
	);

	outputJson($error);
}

?>
