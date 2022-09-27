<?php
header('Content-type: text/html; charset=utf-8');
class parser{
	public $counter;
	public $postResult;
	public $servername = "localhost";
	public $database = "parser";
	public $username = "root";
	public $password = "";
	function newsite(){
		// Устанавливаем соединение
		$url = 'Url';
		$ch = curl_init();
		$agent = $_SERVER["HTTP_USER_AGENT"];
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, "gd_order=0");
		$this->postResult = curl_exec($ch);
	}

	// выполняем запрос для авторизации
	function newrow(){
		$test = strpos($this->postResult,'Полное наименование:');
		$test = $test+85;
		$testend = strpos($this->postResult,'Сокращенное наименование:');
		$testend = $testend - 88;
		$testend = $testend - $test;
		$fullname = substr($this->postResult,$test,$testend);
		echo $fullname;
		$test = strpos($this->postResult,'"Интернет":');
		$test = $test+66;
		$testend = strpos($this->postResult,'ИНН:');
		$testend = $testend - 88;
		$testend = $testend - $test;
		$internet = substr($this->postResult,$test,$testend);
		echo $internet;
		$test = strpos($this->postResult,'Адрес, место нахождения:');
		$test = $test+91;
		$testend = strpos($this->postResult,'Адрес официального сайта в сети "Интернет":');
		$testend = $testend - 254;
		$testend = $testend - $test;
		$adress = substr($this->postResult,$test);
		$adressendpos = strpos($adress,'</div>');
		$adress = substr($this->postResult,$test,$adressendpos);
		echo $adress;
		$checker = substr($internet,0,7);
		if(($checker == 'https:/')||($checker == 'http://')){
				$internetprotocol = $internet;
		}else{
			$internetprotocol = 'http://'.$internet.'';
		}
		$ch = curl_init();
		$agent = $_SERVER["HTTP_USER_AGENT"];
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_URL, $internetprotocol );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, "gd_order=0");
		$this->postResult = curl_exec($ch);
		if(empty($this->postResult)){
			$internetprotocol = 'https://'.$internet.'';
			$ch = curl_init();
			$agent = $_SERVER["HTTP_USER_AGENT"];
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_URL, $internetprotocol );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, "gd_order=0");
			$this->postResult = curl_exec($ch);
		}
		if(strpos($this->postResult,'+79')){
			$test = strpos($this->postResult,'+79');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7(')){
			$test = strpos($this->postResult,'+7(');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+74')){
			$test = strpos($this->postResult,'+74');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7 9')){
			$test = strpos($this->postResult,'+7 9');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7 (')){
			$test = strpos($this->postResult,'+7 (');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7 4')){
			$test = strpos($this->postResult,'+7 4');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7 ')){
			$test = strpos($this->postResult,'+7 ');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+7(')){
			$test = strpos($this->postResult,'+7(');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'+74')){
			$test = strpos($this->postResult,'+74');
			$phone = substr($this->postResult,$test,18);
		}elseif(strpos($this->postResult,'8 9')){
			$test = strpos($this->postResult,'8 9');
			$phone = substr($this->postResult,$test,17);
		}elseif(strpos($this->postResult,'8 (9')){
			$test = strpos($this->postResult,'8 (9');
			$phone = substr($this->postResult,$test,17);
		}elseif(strpos($this->postResult,'8 4')){
			$test = strpos($this->postResult,'8 4');
			$phone = substr($this->postResult,$test,17);
		}elseif(strpos($this->postResult,'8 (4')){
			$test = strpos($this->postResult,'8 (4');
			$phone = substr($this->postResult,$test,17);
		}
		// Устанавливаем соединение
		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
		// Проверяем соединение
		$sql = "INSERT INTO result (id_from_site, fullname, internet, adress, phone) VALUES ('$this->counter', '$fullname', '$internet','$adress','$phone')";
		if (mysqli_query($conn, $sql)) {
			echo '<br>';
	    	echo "New recordcreatedsuccessfully";
		} else {
	    	echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		}
		mysqli_close($conn);
	}
	function last_site(){
		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->database);
		// Проверяем соединение
		$sql = "SELECT max(id_from_site) FROM result";
		$result=$conn->query($sql);
		$resultrow=$result->fetch_assoc();
		$this->counter=$resultrow['max(id_from_site)'];
		mysqli_close($conn);
	}
}
$class = new parser();
	echo $class->counter;
	echo '<br>';
	$class->last_site();
	while ($class->counter < 200000) {
		$class->newsite();
		if(!empty($class->postResult)){
			$class->newrow();
			$class->counter=$class->counter+1;
		}else{
			$class->counter=$class->counter+1;
			echo $class->counter;
			echo '<br>';
			echo 'пусто';
			echo '<br>';
		}
	}