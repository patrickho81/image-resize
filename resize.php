<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Image Resize</title>
<?php
/**
 * Scale an image according to input canvas dimensions. 
 * No cropping will occur, the scaled image is "centered" to canvas.
 */

function imageToCanvas ($_image, $_canvasWidth, $_canvasHeight, $color='white' ,$extension, $forceScale=false)
{
    if (is_string($_image)) {
        if (is_file($_image)) {
            if($extension=="jpg" || $extension=="jpeg" ){
				$_image = imagecreatefromjpeg($_image);
			}
			else if($extension=="png"){
				$_image = imagecreatefrompng($_image);
			}else {
				$_image = imagecreatefromgif($_image);
			}
        } else {
            return "Incorrect imagepath >> No file here " . $_image;
        }
    } 
    
    $width = imagesx($_image);
    $height = imagesy($_image);
    $image_aspect_ratio = $width / $height;
    $canvas_aspect_ratio = $_canvasWidth / $_canvasHeight;
    
    if($forceScale == 'true')
    {
        // scale by height
        if ($image_aspect_ratio < $canvas_aspect_ratio) {
                $new_height = $_canvasHeight;
                $new_width = ($new_height/$height) * $width;
        } 
        // scale by width
        else {
                $new_width = $_canvasWidth;
                $new_height = ($new_width/$width) * $height;
        }
    }
    else
    {
        if($width > $_canvasWidth)
        {
                $new_width = $_canvasWidth;
                $new_height = ($new_width/$width) * $height;
        }
        elseif($height > $_canvasHeight)
        {
                $new_height = $_canvasHeight;
                $new_width = ($new_height/$height) * $width;
        }
        else
        {
                $new_height = $height;
                $new_width = $width;
        }       
    }
    
    # offset values (ie. center the resized image to canvas)
    $xoffset = ($_canvasWidth - $new_width) / 2;
    $yoffset = ($_canvasHeight - $new_height) / 2;
    
    $image_resized = imagecreatetruecolor($_canvasWidth, $_canvasHeight);
    
    # fill colour
	if ($color == 'white'){
	    $fill_color = imagecolorallocate($image_resized, 255, 255, 255);
	}else if ($color == 'black'){
		$fill_color = imagecolorallocate($image_resized, 0, 0, 0);
	}else if ($color == 'tran'){		
		$black = imagecolorallocate($image_resized, 0, 0, 0);
		imagecolortransparent($fill_color, $black);
		//$fill_color = imagecolortransparent($image_resized);
		//$fill_color = $image_resized;
	}
    imagefill($image_resized, 0, 0, $fill_color);
    imagecopyresampled($image_resized, $_image, $xoffset, $yoffset, 0, 0, $new_width, $new_height, $width, $height);
    
    imagedestroy($_image);
    return $image_resized;
}

$forceScale = $_POST['forceScale'];
$color = $_POST['imgbgcolor'];
error_reporting(0);
$change="";
$abc="";
 define ("MAX_SIZE","2048");
 function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }
 $errors=0;
 if($_SERVER["REQUEST_METHOD"] == "POST")
 {
 	$image =$_FILES["file"]["name"];
	$uploadedfile = $_FILES['file']['tmp_name'];
 	if ($image) 
 	{
		$filename = stripslashes($_FILES['file']['name']);
  		$extension = getExtension($filename);
 		$extension = strtolower($extension);
 if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) 
 		{
 			$change='<div class="msgdiv">Unknown Image extension </div> ';
 			$errors=1;
 		}
 		else
 		{
 $size=filesize($_FILES['file']['tmp_name']);
if ($size > MAX_SIZE*1024)
{
	$change='<div class="msgdiv">You have exceeded the size limit!</div> ';
	$errors=1;
}

	$uploadedfile = $_FILES['file']['tmp_name'];
	$tmp = imageToCanvas($uploadedfile, 400, 400, $color, $extension, $forceScale);
	$tmp1 = imageToCanvas($uploadedfile, 60, 60, $color, $extension, $forceScale);


$filename = "images/". $_FILES['file']['name'];
$filename1 = "images/60/". $_FILES['file']['name'];
imagejpeg($tmp,$filename,100);
imagejpeg($tmp1,$filename1,100);
//imagedestroy($src);
imagedestroy($tmp);
imagedestroy($tmp1);
}}
}

//If no errors registred, print the success message
 if(isset($_POST['Submit']) && !$errors)
 {
   // mysql_query("update {$prefix}users set img='$big',img_small='$small' where user_id='$user'");
 	$change=' <div class="msgdiv">Image Uploaded Successfully!</div>';
 }

?>
  <style type="text/css">
.help{
	font-size:11px; color:#006600;
}
body {
     color: #000000;
 	background-color:#999999 ;
    background:#999999 url(<?php echo $user_row['img_src']; ?>) fixed repeat top left;
	font-family:"Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif; 
}
.msgdiv{
	width:759px;
	padding-top:8px;
	padding-bottom:8px;
	background-color: #fff;
	font-weight:bold;
	font-size:18px;-moz-border-radius: 6px;-webkit-border-radius: 6px;
}
#container{
	width:763px;margin:0 auto;padding:3px 0;text-align:left;position:relative; -moz-border-radius: 6px;-webkit-border-radius: 6px; background-color:#FFFFFF }
</style>
</head>

<body>



   <div align="center" id="err">
<?php echo $change; ?>  </div>
   <div id="space"></div>
  <div id="container" >
   <div id="con">
        <table width="502" cellpadding="0" cellspacing="0" id="main">
          <tbody>
            <tr>
              <td width="500" height="238" valign="top" id="main_right">
			  <div id="posts">
			  &nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $filename; ?>" />  
              &nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo $filename1; ?>"  />
			    <form method="post" action="" enctype="multipart/form-data" name="form1">
				<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">
               <tr><Td style="height:25px">&nbsp;</Td></tr>
		<tr>
          <td width="150"><div align="right" class="titles">Picture : </div></td>
          <td width="350" align="left">
            <div align="left">
              <input size="25" name="file" type="file" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:10pt" class="box"/>
              </div></td>
        </tr>
		<tr><Td></Td>
		<Td valign="top" height="35px" class="help">Image maximum size <b>2 </b>MB</span></Td>
		</tr>
        <tr><Td><div align="right" class="titles">Canvas Background Color :</div></Td><td>
        <input type="radio" name="imgbgcolor" value="white" checked /> White BG&nbsp;&nbsp;&nbsp;
        <input type="radio" name="imgbgcolor" value="black" /> Black BG&nbsp;&nbsp;&nbsp;
        <!--input type="radio" name="imgbgcolor" value="tran" /> Transparent --></td></tr>
        <tr><Td><div align="right" class="titles">Force Scale :</div></Td><td>
        <input type="radio" name="forceScale" value="true" checked /> Yes&nbsp;&nbsp;&nbsp;
		<input type="radio" name="forceScale" value="false" />No</td></tr>
		<tr><Td></Td><Td valign="top" height="35px">
        <input type="submit" id="mybut" value="       Upload        " name="Submit"/></Td></tr>
        <tr>
          <td width="200">&nbsp;</td>
          <td width="200"><table width="200" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="200" align="center"><div align="left"></div></td>
                <td width="100">&nbsp;</td>
              </tr>
          </table></td>
        </tr>
      </table>
				</form>
			  </div>
			  </td>
            </tr>
          </tbody>
     </table>
</div>
</div>
</body>
</html>