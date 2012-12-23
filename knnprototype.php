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
	
		public $attributelist = array();
		public $class;
		
		/**
		*
		*	Constructor for a new datapoint
		*	@param int $attributes The attributes of a datapoint.
		*	@param int $class The class value for the datapoint.
		*
		*/
		
		function __construct( $class, $attributes )
		{
			
			$this->class 	=	$class;
			
			foreach( $attributes as $nm => $vl )
			{
				
				$this->attributelist[ $nm ]	=	$vl;	
				
			}
		
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
		*	@param array $attributes The attributes for a datapoint.
		*	@param int $class The class value for the datapoint.
		*
		*/
		
		function addPoint( $class, $attributes )
		{
			
			$this->pointlist[ $class ][]	=	new datapoint( $class, $attributes );
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
		
		function classify( $attributes, $measure = "manhattan" )
		{
			
			// First calculate the prototype value..
			
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
		
		function manhattandistance( $attributes )
		{
			
			$distance	=	array();
			
			foreach( $this->prototype as $name => $prototype )
			{
			
				$distance[$name]	=	0;
				
				foreach( $prototype as $atnm => $attr )
				{
					
					$distance[$name]	=	$distance[$name] + abs( $attr - $attributes[ $atnm ] );
					
				}
				
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
		
		function euclidiandistance( $attributes )
		{
			
			$distance	=	array();
			
			foreach( $this->prototype as $name => $prototype )
			{
			
				$distance[$name]	=	0;
				
				foreach( $prototype as $atnm => $attr )
				{
					
					$distance[$name]	=	$distance[$name] + pow(abs( $attr - $attributes[ $atnm ] ));
					
				}
				
				$distance[$name]	=	sqrt( $distance[$name] );
				
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
		
		function calculatePrototype()
		{
			
			$prototype	=	array();
			
			foreach( $this->pointlist as $classname => $classvalues )
			{
			
				$values	=	array();
				
				foreach( $classvalues as $instance )
				{
					
					foreach( $instance->attributelist as $attributename => $attributevalue )
					{
						
						$values[ $attributename ][]	=	$attributevalue;
						
					}
					
				}
				
				foreach( $values as $attributename => $val )
				{
					
					$prototype[$classname][$attributename]	= $this->array_avg($val);
					
				}
				
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
	
	$classifier->addPoint( "+", array( "x" => 10, "y" => 15, "m" => 2 ) );
	$classifier->addPoint( "v", array( "x" => 11, "y" => 12, "m" => 1000 ) );
	$classifier->addPoint( "-", array( "x" => 3, "y" => 6, "m" => 10 ) );
	
	echo $classifier->classify( array( "x" => 11, "y" => 12, "m" => 700 ) );

?>