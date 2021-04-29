<?php

function getConnection() {
	$servername = "localhost";
	$uname = "phpmyadmin";
	$pword = "sab95978";
	$dbname = "websecurity";
	$conn = new PDO("mysql:host=$servername;dbname=$dbname", $uname, $pword);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn;
}

$currentlang = "en";
if (array_key_exists('lang', $_GET)) {
	$currentlang = $_GET['lang'];
}

try {
	$sql = "SHOW TABLES LIKE 'lab_languages';";
	$stmt = getConnection()->prepare($sql);
	$stmt->execute();
	if ($stmt->rowCount() == 0) {
		$sql = "CREATE TABLE lab_languages (langid varchar(2) NOT NULL,    name varchar(50) NOT NULL,    hello varchar(50) NOT NULL  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; INSERT INTO lab_languages (langid, name, hello) VALUES  ('en', 'U.S.', 'Hello'),  ('es', 'Spanish', 'Hola'),  ('gr', 'German', 'Guten Tag'),  ('it', 'Italian', 'Salve'),  ('jp', 'Japanese', 'Konnichiwa'); ALTER TABLE lab_languages    ADD PRIMARY KEY (langid);";
		$stmt = getConnection()->prepare($sql);
		$stmt->execute();
	}
} catch(PDOException $e) {
	throw new Exception($e->getMessage());
}

$langs = [];
try {
	$sql = "SELECT * FROM lab_languages ORDER BY name";
	$stmt = getConnection()->prepare($sql);
	$stmt->execute();
	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$langs = $stmt->fetchAll();
} catch(PDOException $e) {
	throw new Exception($e->getMessage());
}

$hello = "Hello";
$country = "U.S.";
foreach ($langs as $lang) {
	if($lang['langid'] == $currentlang) {
		$hello = $lang['hello'];
		$country = $lang['name'];
	}
}

?>
<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Finding vulnerabilities in source code</title>
	<meta name="author" content="Russell Thackston">
</head>
<body style="background-color: lightgray;">
	<h1><?php echo $country; ?></h1>
	<div>
		<select name="lang" id="lang">
			<?php foreach ($langs as $lang) { ?>
				<option value="<?php echo $lang['langid'] ?>" <?php if ($lang['langid'] == $currentlang) { echo "selected"; } ?>><?php echo $lang['name'] ?></option>
			<?php } ?>
		</select>
	</div>
	<div>
		<?php echo file_get_contents($currentlang); ?>
	</div>
	<script>
		let dd = document.getElementById("lang");
		dd.addEventListener("change", function(e) {
			window.location.href = "index.php?lang=" + dd.value;
		});
	</script>
</body>
</html>
