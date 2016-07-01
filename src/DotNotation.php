<?php
/**
 * This file is part of {@see \arabcoders\DotNotation} package.
 *
 * (c) 2015-2016 Abdul.Mohsen B. A. A..
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace arabcoders\DotNotation;

/**
 * Array Dot Notation
 *
 * @author Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */
class DotNotation
{
    /**
     * @var string default separator.
     */
    const SEPARATOR = '/[:\.]/';

    /**
     * @var string
     */
    private $separator;

    /**
     * @var array
     */
    protected $values = [ ];

    /**
     * constructor.
     *
     * @param array $values
     * @param array $options
     */
    public function __construct( array $values, array $options = [ ] )
    {
        $this->values = $values;

        $this->separator = ( empty( $options['separator'] ) ) ? self::SEPARATOR : $this->separator;
    }

    /**
     * get value.
     *
     * @param string      $path
     * @param string|null $default
     *
     * @return mixed
     */
    public function get( string $path, $default = null )
    {
        $array = $this->values;

        if ( !empty( $path ) )
        {
            $keys = $this->explode( $path );

            foreach ( $keys as $key )
            {
                if ( isset( $array[$key] ) )
                {
                    $array = $array[$key];
                }
                else
                {
                    return $default;
                }
            }
        }

        return $array;
    }

    /**
     * set key/value.
     *
     * @param string $path
     * @param mixed $value
     *
     * @throws \RuntimeException
     *
     * @return DotNotation
     */
    public function set( string $path, $value ): DotNotation
    {
        if ( !empty( $path ) )
        {
            $at = &$this->values;

            $keys = $this->explode( $path );

            while ( count( $keys ) > 0 )
            {
                if ( count( $keys ) === 1 )
                {
                    if ( is_array( $at ) )
                    {
                        $at[array_shift( $keys )] = $value;
                    }
                    else
                    {
                        throw new \RuntimeException( "Can not set value at this path ($path) because is not array." );
                    }
                }
                else
                {
                    $key = array_shift( $keys );
                    if ( !isset( $at[$key] ) )
                    {
                        $at[$key] = [ ];
                    }
                    $at = &$at[$key];
                }
            }
        }
        else
        {
            $this->values = $value;
        }

        return $this;
    }

    /**
     * add value to path.
     *
     * @param string $path
     * @param array  $values
     *
     * @return DotNotation
     */
    public function add( string $path, array $values ): DotNotation
    {
        $get = (array) $this->get( $path );
        $this->set( $path, $this->arrayMergeRecursiveDistinct( $get, $values ) );

        return $this;
    }

    /**
     * Delete key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete( string $key ): bool
    {
        unset( $this->values[$key] );

        return true;
    }

    /**
     * have key.
     *
     * @param string $path
     *
     * @return bool
     */
    public function have( string $path ): bool
    {
        $keys  = $this->explode( $path );
        $array = $this->values;
        foreach ( $keys as $key )
        {
            if ( isset( $array[$key] ) )
            {
                $array = $array[$key];
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * set Values.
     *
     * @param array $values
     *
     * @return DotNotation;
     */
    public function setValues( array $values ): DotNotation
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get Values.
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * explode string path.
     *
     * @param string $path
     *
     * @return array
     */
    protected function explode( string $path ): array
    {
        return preg_split( self::SEPARATOR, $path );
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * If key is integer, it will be merged like array_merge do:
     * arrayMergeRecursiveDistinct(array(0 => 'org value'), array(0 => 'new value'));
     *     => array(0 => 'org value', 1 => 'new value');
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    protected function arrayMergeRecursiveDistinct( array &$array1, array &$array2 ): array
    {
        $merged = $array1;
        foreach ( $array2 as $key => &$value )
        {
            if ( is_array( $value ) && isset ( $merged[$key] ) && is_array( $merged[$key] ) )
            {
                if ( is_int( $key ) )
                {
                    $merged[] = $this->arrayMergeRecursiveDistinct( $merged[$key], $value );
                }
                else
                {
                    $merged[$key] = $this->arrayMergeRecursiveDistinct( $merged[$key], $value );
                }
            }
            else
            {
                if ( is_int( $key ) )
                {
                    $merged[] = $value;
                }
                else
                {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }
}