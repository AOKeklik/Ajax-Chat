<?php 
	$vt = new PDO("mysql:host=localhost;dbname=chat;charset=utf8", 'root', '');

// chat class
	class Chat{
		public $arkarenk, $yazirenk;
	// users to list
		public function kisigetir($vt){
			$uye = $vt -> prepare("SELECT * FROM kisiler");
			$uye -> execute();
			
			while($geldi = $uye -> fetch(PDO::FETCH_ASSOC)):
				if($geldi['durum'] == 1):
					echo "<span class='text-success'>".$geldi['ad']." - Online </span>";
				else:
					echo "<span class='text-danger'>".$geldi['ad']." - Ofline </span>";
				endif;
			endwhile;
		}
	// users query
		public function giriskontrol($vt, $kulad, $sifre){
			$sor = $vt -> prepare("SELECT * FROM kisiler WHERE ad='$kulad' AND sifre='$sifre'");
			$sor -> execute();
			$veri = $sor -> fetch(PDO::FETCH_ASSOC);
			
			if($sor -> rowCount() == 0):
				echo "<div class='alert alert-danger'>Bilgiler Hatali..</div>";
				header('refresh:2, url=index.php');
			else:
				setcookie('kisiad', $kulad);
			
				$sor2 = $vt -> prepare("UPDATE kisiler SET durum=1 WHERE ad='$kulad'");
				$sor2 -> execute();
			
				echo "<div class='alert alert-success'>Giris Yapiliyor..</div>";
				header('refresh:2, url=chat.php');
			endif;
		}
	// cookie users controls
		public function oturumkontrol($vt, $durum = false){
			if(isset($_COOKIE['kisiad'])):
				$kisiad = $_COOKIE['kisiad'];
				
				$sor = $vt -> prepare("SELECT * FROM kisiler WHERE ad='$kisiad'");
				$sor -> execute();
				$veri = $sor -> fetch(PDO::FETCH_ASSOC);
			
				if($sor -> rowCount() == 0):
					header("Location:index.php");
				else:
					if($durum == true) header("Location:chat.php");
				endif;
			else:
				if($durum == false) header("Location:index.php");
			endif;
		}
	// color to choose
		public function renklerebak($vt){
			$kisiad = $_COOKIE['kisiad'];
			
			$sor = $vt -> prepare("SELECT * FROM kisiler WHERE ad='$kisiad'");
			$sor -> execute();
			$veri = $sor -> fetch(PDO::FETCH_ASSOC);
			
			$this -> arkarenk = $veri['arkarenk'];
			$this -> yazirenk = $veri['yazirenk'];
		} 
	}

// chat functionss
	@$chat = $_GET['chat'];
	
	switch($chat):
	// tolist comments from chat.txt file
		case 'oku':
			$dosya = fopen('konusmalar.txt', 'r');

			while(!feof($dosya)):
				$satir = fgets($dosya);
				print($satir);
			endwhile;

			fclose($dosya);
		break;
	// toadd veriables to files
		case 'ekle':
			$kisiad = $_COOKIE['kisiad'];
	
			$sor = $vt ->  prepare("SELECT * FROM kisiler WHERE ad='$kisiad'");
			$sor -> execute();
			$veri = $sor -> fetch(PDO::FETCH_ASSOC);

			$mesaj = htmlspecialchars(strip_tags($_POST['mesaj']));

			fwrite(fopen('konusmalar.txt', 'a'), '<span class="pb-5" style="color:#'.$veri['yazirenk'].'"><kbd style="background-color:#'.$veri['arkarenk'].'">'.$kisiad.'</kbd>'.$mesaj.'</span><br>');							
		break;
	// exits
		case 'cikis':
			$kisiad = $_COOKIE['kisiad'];

			$sor = $vt -> prepare("UPDATE kisiler SET durum=0 WHERE ad='$kisiad'");
			$sor -> execute();

			setcookie('kisiad', $kulad, time() - 1);

			header("Location:index.php");
		break;
	// comments settings
		case 'sohbetayar':
			if($_POST):
				$secenek = $_POST['secenek'];

				if($secenek == 'temizle'):
					unlink('konusmalar.txt');
					touch('konusmalar.txt');

					echo "<div class='alert alert-success mt-3'>Sohbet Temizlendi..</div>";
				elseif($secenek == 'kaydet'):
					copy('konusmalar.txt', 'kaydedilenler/'.date('d.m.Y').'-konusma.txt');

					echo '<div class="alert alert-success mt-3">Sohbet Depolandi..</div>';
				endif;
			endif;
		break;
	// background color 
		case 'arkarenk':
			if($_POST):
				$arkaplankod = $_POST['arkaplankod'];
				$kisiad = $_COOKIE['kisiad'];

				$sor = $vt -> prepare("UPDATE kisiler SET arkarenk='$arkaplankod' WHERE ad='$kisiad'");
				$sor -> execute();

				echo '<div class="alert alert-success mt-3">Arkaplan Rengi Degistirildi..</div>';
			endif;
		break;
	// txt color 
		case 'yazirenk':
			if($_POST):
				$yazirenkkod = $_POST['yazirenkkod'];
				$kisiad = $_COOKIE['kisiad'];

				$sor = $vt -> prepare("UPDATE kisiler SET yazirenk='$yazirenkkod' WHERE ad='$kisiad'");
				$sor -> execute();

				echo '<div class="alert alert-success mt-3">Yazi Rengi Degistirildi..</div>';
			endif;
		break;
	// comments info
		case "ortak":
 			if($_GET["uyead"]!="") :
 				fwrite(fopen("kisiler.txt","a"),'<span class="pb-5">'.$_GET["uyead"].' Yazıyor...</span><br>');
 			endif;
 			if ($_GET["temizle"]!="") :
 				$dosya="kisiler.txt";
               	$ac=fopen($dosya,"r");
               	$oku=fread($ac,filesize($dosya));

 				$str=str_replace('<span class="pb-5">'.$_GET["temizle"].' Yazıyor...</span><br>',"",$oku);
 				$yaz="kisiler.txt";
 				$yazd=	fopen($yaz,"w");
 						fwrite($yazd,$str);
 						fclose($yazd);
 			endif;
		case "dosyaoku":
 			$dosya=fopen("kisiler.txt", "r");	

 			while (!feof($dosya)):
				$satir=fgets($dosya);
				print($satir);	
			endwhile;

			fclose($dosya);
 		break;
	endswitch;
	


?>