<?php
//	$content = filter_var($quote['content'], FILTER_SANITIZE_STRING);
	$content = stripslashes($quote['content']);
	$content = explodeQuote($content);

	if (empty($quote['syntaxHighlighting'])) {
		$content = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $content);
		$content = findUsernames($content);
	}


echo '<div class = "quoteContainer">';
	echo '<strong><a href = "show.php?action=show&amp;id=' . $quote['id'] . '">#' . $quote['id'] . '</a></strong>';
	
	if (isAdmin()) {
		echo ' <small>[</small>';
		echo '<ul class = "admin">';
		echo '<li><a href = "edit.php?id=' . $quote['id'] . '">edit</a></li>';
		if (basename($_SERVER['PHP_SELF']) == 'approvals.php') {
			if ($quote['approval'] == '0') {
				echo '<li><a href = "approvals.php?approveId=' . $quote['id'] . '">approve</a></li>';
			}
		}
		echo '<li><a href = "delete.php?id=' . $quote['id'] . '">delete</a></li>';

		echo '</ul><small>]</small>';
	}


	if (!empty($quote['syntaxHighlighting'])) {
		echo '<pre class = "codeSnippit ' . $quote['syntaxHighlighting'] . '">';
			foreach ($content as $line) {
				echo $line['content'] . "\n";
			}
		echo '</pre>';
	} else {
		echo '<p class = "quote">';
			foreach ($content as $line) {
				if (empty($line['username'])) {
					echo $line['content'] . '<br />';
				} else {
					echo '<strong style = "color: ' . $line['usernameColor'] . '">' . $line['username'] . '</strong>: ' . $line['content'] . '<br />';
				}
			}
		echo '</p>';
	}

echo '</div>';
?>