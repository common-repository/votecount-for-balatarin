<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Votecount for Balatarin</title>
<style> 

	* {
		margin: 0;
		padding: 0
	}
	
	a {
		text-decoration:none;
	}
	
	.bvc_vote .bvc_votecount {
    	display: block;
    	background-color: #fff;
    	color: #000;
    	font-family:"Helvetica",tahoma,verdana,arial,sans-serif;
    	text-align: center;
    	height: 22px;
		width: 38px;
		font-size: 18px;
		background:no-repeat top right;
		background-image: url('sprite.png');
		background-position: -10px -82px;
	}
	
	.bvc_vote .bvc_votebutton { 
		display: block;
		width: 38px;
		height: 16px;
		text-align:left;		
		background:no-repeat top right;
		background-image: url('sprite.png');
		background-position: -10px -124px;
	}
	
	.bvc_vote .bvc_votebutton2 { 
		display: block;
		width: 38px;
		height: 16px; 
		text-align:left;
		background:no-repeat top right;
		background-image: url('sprite.png');
		background-position: -10px -10px;
	}
	
	.bvc_vote a.bvc_votebutton:hover { 
		background:no-repeat top right;
		background-image: url('sprite.png');
		background-position: -10px -160px;
	}
	
	.bvc_vote a.bvc_votebutton2:hover { 
		background:no-repeat top right;
		background-image: url('sprite.png');
		background-position: -10px -46px;
	}

</style>
<script>
</script>
</head>
<body>
<?php
$bvcurl = $_GET['bvcurl'];
$url = $_GET['url'];
$title = $_GET['title'];
$donate = $_GET['donate'];

$tmp = explode('/',$bvcurl);
$id = $tmp[3];
$bvcurl = 'http://www.balatarin.com/permlink/' . $bvcurl;


if ($bvcurl === 'http://www.balatarin.com/permlink/') {
     $string = '0';
	 $url = 'http://www.balatarin.com/links/submit?phase=2&url=' . $url . '&title=' . $title;
} else {
     $html = file_get_contents($bvcurl);
     preg_match_all('|id="vn' . $id . '">\n(.*)<|Ums',$html,$out, PREG_PATTERN_ORDER);
     $string = trim($out[1][0]);
	 $url = $bvcurl;
}

if ($donate === '0') {
     $donateurl = '';
	 $donatetitle = '';
	 $bvccount = $string;
} else {
     $donateurl = 'http://www.tafreevar.com/wordpress-plugins/votecount-for-balatarin';
	 $donatetitle = 'Click here to use this tool in your own blog!';
	 $bvccount = '<a href="' . $donateurl . '" title="' . $donatetitle . '" target="_blank">' . $string . '</a>';
}

?>

	<div class="bvc_vote">
	<a href="<?php echo $url; ?>" 
    	title="+1" 
    	class="bvc_votebutton2"  
    	target="_blank"> </a>
		    <div class="bvc_votecount">
    			<span class="count" title="0 total clicks"><?php echo $bvccount; ?></span>
    		</div>
		
    <a href="<?php echo $url; ?>" 
    	title="-1" 
    	class="bvc_votebutton"  
    	target="_blank"> </a>
</div>

</body>
</html>