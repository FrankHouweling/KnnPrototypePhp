<?php
require_once "knnprototype.php";

$classifier	=	new classifier();

$x 		= new attributeclass( "x", 0.1 );
$y 		= new attributeclass( "y", 0.1 );
$m 		= new attributeclass( "m", 1 );

$class1	= $classifier->addClassifyClass("+");
$class2 = $classifier->addClassifyClass("v");
$class3 = $classifier->addClassifyClass("-"); 

$class1->addPoint( array( new attributevalue($x, 10),  new attributevalue($y, 15),  new attributevalue($m, 2) ) );
$class1->addPoint( array( new attributevalue($x, 20),  new attributevalue($y, 30),  new attributevalue($m, 4) ) );
$class2->addPoint( array(  new attributevalue($x, 11),  new attributevalue($y, 12),  new attributevalue($m, 1000) ) );
$class3->addPoint( array( new attributevalue($x, 3), new attributevalue($y, 6), new attributevalue($m, 10) ) );

echo $classifier->classify( array(  new attributevalue($x, 3),  new attributevalue($y, 6),  new attributevalue($m, 1000) ), "euclidian" );

?>