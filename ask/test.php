<?php

srand((double)microtime() * 1000000);
//create a random number feed.
//$ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
$ychar = "0,1,2,3,4,5,6,7,8,9";
$randnum=0;
$authnum=0;
$list = explode(",", $ychar);
for ($i = 0; $i < 5; $i++) {
	$randnum = rand(0, 9);
	// 10+26;
	$authnum .= $list[$randnum];
}
echo $authnum;
?>