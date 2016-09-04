<?php

namespace TeamZac\TexasComptroller\Support;

use Carbon\Carbon;

class AttributeBag
{
    /** @var array */
    protected $attributes = [];

    /** 
     * An associative array of keys and how to mutate the values
     * 
     * @var array 
     */
    protected $casts = [];

    /** @var string */
    protected $dateFormat = 'Ymd';

    /** @var array */
    protected static $attributeKeys = [];

    /**
     * Constructor
     */
    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Set the attributes array from the data provided, adding
     * keys and values in place of the integer indexes
     * 
     * @param   array $attributes
     * @return  void
     */
    public function setAttributes($attributes = [])
    {
        foreach ($attributes as $index=>$value)
        {
            // sanity check that we do have an index in the key map
            if ( ! array_key_exists($index, static::$attributeKeys) )
            {
                continue;
            }

            $key = static::$attributeKeys[$index];

            if ( $key == 'filler' )
            {
                continue;
            }

            $this->attributes[static::$attributeKeys[$index]] = trim($value);
        }
    }

    /**
     * 
     * 
     * @param   
     * @return  
     */
    public function __get($key)
    {
        if ( array_key_exists($key, $this->attributes) )
        {
            return $this->castAttribute($key);
        }
    }

    /**
     * Cast the attribute if a mutator exists for the key
     * 
     * @param   string $key
     * @return  mixed
     */
    public function castAttribute($key)
    {
        if ( ! array_key_exists($key, $this->attributes) )
        {
            return null;
        }

        if ( ! array_key_exists($key, $this->casts) ) 
        {
            return $this->attributes[$key];
        }

        $value = $this->attributes[$key];

        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'object':
                return $this->fromJson($value, true);
            case 'array':
            case 'json':
                return $this->fromJson($value);
            case 'collection':
                return new BaseCollection($this->fromJson($value));
            case 'date':
                return $this->asDateTime($value);
            default:
                return $value;
        }
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param  string  $key
     * @return string
     */
    protected function getCastType($key)
    {
        return trim(strtolower($this->casts[$key]));
    }


    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to re-instantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Carbon) {
            return $value;
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        return Carbon::createFromFormat($this->getDateFormat(), $value)->startOfDay();
    }

    /**
     * Get the date format
     * 
     * @return  string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }
}