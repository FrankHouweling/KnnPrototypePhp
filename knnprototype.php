<?php


	/**
	*
	*	Knn Prototype
	*	A simple, smart and fast classifier written in PHP.
	*	@author Frank Houweling <houweling.frank@gmail.com>
	*	@version 1.1b
	*
	*	Known issues:
	*	Only supports two attributes: x and y, multiple attributes can be added in the future.
	*
	*/
	
	/**
	*
	*	Class datapoint
	*	General datapoint class.
	*
	*/

	class datapoint
	{
		public $attributelist = array(), $class;
		
		/**
		*
		*	Constructor for a new datapoint
		*	@param int $attributes The attributes of a datapoint.
		*
		*/
		
		function __construct( $attributes ){
			foreach( $attributes as $nm => $vl )
				$this->attributelist[ $nm ]	=	$vl;
		}
	}
	
	class attributevalue
	{
		public $attributeclass, $val;
		
		function __construct( attributeclass $attributeclass, $val ){
			$this->attributeclass = $attributeclass;
			$this->val = $val;
		}
	}
	
	class attributeclass
	{
		public $name, $weight, $pointlist = array();
		
		function __construct( $name, $weight )
		{
			$this->name   = $name;
			$this->weight = $weight;
		}
		
	}
	
	class classifyclass
	{
		public $name, $pointlist = array();
		
		function __construct( $name )
		{
			$this->name = $name;
		}
		
		/**
		*
		*	Add a new point to the trainingdata
		*	@param array $attributes The attributes for a datapoint.
		*	@param int $class The class value for the datapoint.
		*
		*/
		
		function addPoint( $attributes )
		{
			$this->pointlist[] = new datapoint( $attributes );
		}
	}
	
	/**
	*
	*	Class classifier
	*	A simple Knn prototype based classifier class.
	*
	*/
	
	class classifier
	{
		private $prototype, $classifyableClasses = array();
		
		function __construct(){
			// Empty constructor	
		}
		
		/**
		*
		*	Classify a given datapoint
		*	@param array $attributes The attributes for the to be classified datapoint.
		*	@param string $measure The to be used measure (manhattan or euclidian)
		*
		*	@return string Result class.
		*
		*/
		
		function classify( $attributes, $measure = "manhattan" ){
			// First calculate the prototype value..
			if( empty($this->prototype) )
				$this->calculatePrototype();
			
			// Now we seek the  distance between the prototypes and the new point
			
			switch( $measure )
			{
				case 'manhattan':	
					$distance = $this->manhattandistance( $attributes );
				break;
				case 'euclidian':
					$distance = $this->euclidiandistance( $attributes );
				break;
				default:
					$distance = $this->manhattandistance( $attributes );
				break;
			}
			
			asort( $distance );
			reset( $distance );
			
			return key( $distance );
		}
		
		/**
		*
		*	Calculates the distance between a given datapoint and the last calculated prototypes using Manhattan Distance.
		*	
		*	@param array $attributes The attributes of the given datapoint.
		*
		*	@return array An array with the distances to the different classes.
		*
		*/
		
		function manhattandistance( $attributes ){
			$distance	=	array();
			
			foreach( $this->prototype as $name => $prototype )
			{
				$distance[$name]	=	0;	
				foreach( $prototype as $atnm => $attr )
					$distance[$name]	=	$distance[$name] + abs( $attr - $attributes[ $atnm ] );
			}
			
			return $distance;
		}
		
		/**
		*
		*	Calculates the distance between a given datapoint and the last calculated prototypes using Euclidian Distance.
		*	
		*	@param array $attributes The attributes of the given datapoint.
		*
		*	@return array An array with the distances to the different classes.
		*
		*/
		
		function euclidiandistance( $attributes ){
			$distance	=	array();

			foreach( $attributes as $attribute )
			{
				$attr = $attribute->val;
				$atnm = $attribute->attributeclass->name;
				foreach( $this->prototype as $classname => $prototype )
				{
					foreach( $prototype as $attributename => $attributevalue )
					{
						if( $attributename == $atnm )
						{
							$distance[$classname]	=	$distance[$classname] + ( 
								pow( ( abs( $attr - $attributevalue ) * $attribute->attributeclass->weight ) , 2 ) );
						}
					}
				}
			}
			
			return $distance;
		}
		
		/**
		*
		*	Calculate the prototype values from the training data
		*
		*	@return array An array of the prototypes in the form: array( classname => array( x, y ) )
		*
		*/
		
		function calculatePrototype(){
			$prototype	=	array();
			
			foreach( $this->classifyableClasses as $class )
			{	
				$classname = $class->name;
				$values	=	array();
				
				foreach( $class->pointlist as $datapoint )
				{
					foreach( $datapoint->attributelist as $attrval )
						$values[ $attrval->attributeclass->name ][]	=	$attrval->val;
				}
				
				foreach( $values as $attributename => $val )
					$prototype[$classname][$attributename]	= $this->array_avg($val);
			}
			
			$this->prototype	=	$prototype;
		}
		
		/**
		*
		*	Calculate the avarage value from an array
		*
		*	@param array An array filled with int values.
		*
		*	@return int The avarage value of the values in the given array.
		*
		*/
		
		function array_avg( $array )
		{
			$count	=	count( $array );
			$total	=	0;
			
			foreach( $array as $value )
				$total	=	$total + $value;
			
			return ( $total/$count );
		}
		
		function addClassifyClass( $name )
		{
			$class = new classifyclass( $name );
			$this->classifyableClasses[] = $class;
			
			return $class;
		}
		
	}
	
	// Test if it works..
	
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