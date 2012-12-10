<?php


	/**
	*
	*	Knn Prototype
	*	A simple, smart and fast classifier written in PHP.
	*	@author Frank Houweling <houweling.frank@gmail.com>
	*	@version 1.0b
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
	
		public $x;
		public $y;
		public $class;
		
		/**
		*
		*	Constructor for a new datapoint
		*	@param int $x The first attribute (x) for the datapoint.
		*	@param int $y The second attribute (x) for the datapoint.
		*	@param int $class The class value for the datapoint.
		*
		*/
		
		function __construct( $x, $y, $class )
		{
		
			$this->x		=	$x;
			$this->y		=	$y;
			$this->class 	=	$class;
		
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
		
		private $pointlist	=	array();
		private $prototype;
		
		function __construct()
		{
			
			
			
		}
		
		/**
		*
		*	Add a new point to the trainingdata
		*	@param int $x The first attribute (x) for the datapoint.
		*	@param int $y The second attribute (x) for the datapoint.
		*	@param int $class The class value for the datapoint.
		*
		*/
		
		function addPoint( $x, $y, $class )
		{
			
			$this->pointlist[ $class ][]	=	new datapoint( $x, $y, $class );
		}
		
		/**
		*
		*	Classify a given datapoint
		*	@param int $x The first attribute (x) for the to be classified datapoint.
		*	@param int $y The second attribute (x) for the to be classified datapoint.
		*
		*	@return string Result class.
		*
		*/
		
		function classify( $x, $y )
		{
			
			// First calculate the prototype value..
			
			$this->calculatePrototype();
			
			// Now we seek the manhattan distance between the prototypes and the new point
			
			foreach( $this->prototype as $name => $prototype )
			{
				
				$distance[$name]	=	abs( $prototype["x"] - $x ) + abs( $prototype["y"] - $y );
				
			}
			
			asort( $distance );
			reset( $distance );
			
			return key( $distance );
			
		}
		
		/**
		*
		*	Calculate the prototype values from the training data
		*
		*	@return array An array of the prototypes in the form: array( classname => array( x, y ) )
		*
		*/
		
		function calculatePrototype()
		{
			
			$prototype	=	array();
			
			foreach( $this->pointlist as $classname => $classvalues )
			{
				
				$xvalues	=	array();
				$yvalues	=	array();
				
				foreach( $classvalues as $point )
				{
					
					$xvalues[]	=	$point->x;
					$yvalues[]	=	$point->y;
					
				}
				
				$prototype[$classname]	=	array( "x" => $this->array_avg($xvalues), "y" => $this->array_avg($yvalues) );
				
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
			{
				
				$total	=	$total + $value;
				
			}
			
			return ( $total/$count );
			
		}
		
	}
	
	// Test if it works..
	
	$classifier	=	new classifier();
	
	$classifier->addPoint( 11, 15, "+" );
	$classifier->addPoint( 8, 14, "+" );
	$classifier->addPoint( 7, 20, "+" );
	$classifier->addPoint( 9, 13, "+" );
	
	$classifier->addPoint( 10, 15, "-" );
	$classifier->addPoint( 12, 13, "-" );
	$classifier->addPoint( 14, 12, "-" );
	$classifier->addPoint( 15, 12.5, "-" );
	
	echo $classifier->classify( 13.5,10 );

?>