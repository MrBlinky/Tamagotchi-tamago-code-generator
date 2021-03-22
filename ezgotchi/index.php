<?php
$namechars     = array(' ' ,'A' ,'B' ,'C' ,'D' ,'E' ,'F' ,'G' ,'H' ,'I' ,'J' ,'K' ,'L' ,'M' ,'N' ,'O' ,'P' ,'Q' ,'R' ,'S' ,'T' ,'U' ,'V' ,'W' ,'X' ,'Y' ,'Z' ,'0' ,'1' ,'2' ,'3' ,'4' ,'5' ,'6' ,'7' ,'8' ,'9' ,'.' ,'!' ,'?');
$namechars_xor = array(0xA0,0x11,0x22,0x33,0x44,0x55,0x66,0x77,0x88,0x99,0xAA,0xBB,0xCC,0xDD,0xEE,0xFF,0x01,0x02,0x03,0x04,0x05,0x06,0x07,0x08,0x09,0x0A,0x0B,0x0C,0x0D,0x0E,0x0F,0x10,0x20,0x30,0x40,0x50,0x60,0x70,0x80,0x90);
$digit7_xor    = array(0x11, 0x32, 0x53, 0x74, 0x15, 0x36, 0x58, 0x77, 0x29, 0x4F, 0x6E, 0x0D, 0x2C, 0x4B, 0x6A, 0x7F);
$digit8_xor    = array(0x57,0x3A);

$info='';
$password1 ='';
$password2 ='';
//handle name input
if (isset($_POST["name"])) $name =strtoupper($_POST["name"]); else $name='';
if (strlen($name)>8) {
	$name = '';
	$info='Invalid name';
} else if ($name !='') while (strlen($name)<8) $name .= ' ';
for ($i=0; $i < strlen($name); $i++) {
	if (in_array($name[$i],$namechars) == false) {
		$name = '';
		$info='Invalid name';
		break;
	}
}
//handle points input
if (isset($_POST["points"])) {
	$points = floor($_POST["points"] / 1000);
} else {
	$points = 999;
}
if (($points < 0) or ($points > 999)) $points = 999;

//fill hunger meter
$hunger = 7;

if ($name!='') {
	$digit8 = 0;
	$digit7 = (0x100 - ($hunger << 1) - ($points >> 8) - ($points >> 4) - $points) & 0x0F;
	$password = array(0, 0, 0, $points & 0xFF, ($hunger << 5) | ($points >> 8));
	
	for ($i=0; $i < 5; $i++) {
		$password[$i] ^= $digit7_xor[$digit7];
	}
	$password[0] ^= $namechars_xor[array_search($name[1],$namechars)];
	$password[1] ^= $namechars_xor[array_search($name[5],$namechars)];
	$password[2] ^= $namechars_xor[array_search($name[2],$namechars)];
	$password[3] ^= $namechars_xor[array_search($name[3],$namechars)];
	$password[4] ^= $namechars_xor[array_search($name[0],$namechars)];
	
	for ($i=0;$i < 5; $i++) {
		$password[$i] ^= $digit8_xor[$digit8];
	}
	$info ='Logout password:';
	$password1 =sprintf('%X%X%X%X%X%X',
	$password[2] >> 4, $password[2] & 0xF,
	$password[1] >> 4, $password[1] & 0xF, 
	$password[0] >> 4, $password[0] & 0xF
	);
	$password2 =sprintf('%X%X%X%X%X%X',
	$digit7, $digit8,
	$password[4] >> 4, $password[4] & 0xF, 
	$password[3] >> 4, $password[3] & 0xF
	);
} else $name = 'YOURNAME';	
?>
<html><head><title>EZgotchi points for the Tamagotchi Tama-Go</title></head><body><center>
<div style="background-repeat: no-repeat;background-image:url(blue.jpg);background-position: -55px -25px; width:915px;height:1080px; position:relative;">
<form action="" method="POST">
<input type="text" name="name" value="<?php echo $name; ?>" maxlength="8" onfocus="this.value = ''" style="
background-color: rgba(255, 255, 255, 0.33);border: 3px solid black;border-radius: 16px;width:360px;
position:relative;left:-30px;top:358px;padding:8px;font-size:56px;text-align: center;"><br>
<label style="position:relative;left:-30px;top:570px;font-size:48px;line-height:48px;font-weight:bold;">Points:</label>
<input type="number" name="points" value="<?php echo $points * 1000; ?>" min="0" max="999000" step="1000" style="
background-color: rgba(255, 255, 255, 0.33);border: 3px solid black;border-radius: 16px;width:200px;
position:relative;left:-30px;top:575px;font-size:48px;padding:4px;text-align: center;"><br>
<input type="image" src="btn.png" name="a" style="position:relative;left:-172px;top:660px"><br>
<input type="image" src="btn.png" name="b" style="position:relative;left:-31px;top:593px"><br>
<input type="image" src="btn.png" name="c" style="position:relative;left:112px;top:467px">
</form>
<b style="font-size:40px;line-height:56px;font-family:Sans-serif;position:relative;left:-30px;top:-12px">
<?php echo $info; ?>
</b><br>
<b style="font-size:80px;line-height:70px;font-family:monospace;position:relative;left:-30px;">
<?php echo $password1;?><br><?php echo $password2;?></b>
</div>
<div style="font-size:24px">v1.2</div>
<div style="width:720px;font-size:48px;text-align:left">
<p>Enter the name of your Tamagotchi TamaGo, choose the number of points you want to collect and press any button.
<p>Points must be a multiple of 1000 and in range 0 to 999000.
<p>As of v1.1 your hunger meter will be filled completely.
<P>To enter the logout code on your TamaGo:<ul>
<li>Go to the door icon.
<li>Choose PC.
<li>Don't enter any points.
<li>Ignore the login code.
<li>Enter the logout code.</ul>
</center></div></body></html>
