<?php

class Lana_Email {

	public $id;
	public $user_id;
	public $username;
	public $user_ip;
	public $user_agent;
	public $email_to;
	public $subject;
	public $message;
	public $headers;
	public $date;

	/**
	 * Lana_Email constructor.
	 *
	 * @param array $properties
	 */
	function __construct( array $properties = array() ) {
		$properties = array_intersect_key( $properties, $this->properties() );

		foreach ( $properties as $property => $value ) {
			$this->{$property} = maybe_unserialize( $value );
		}

		/** set user ip */
		$this->user_ip = sanitize_text_field( lana_email_logger_get_user_ip() );

		/** set user agent */
		$this->user_agent = sanitize_text_field( lana_email_logger_get_user_agent() );
	}

	/**
	 * Get primary key
	 * @return string
	 */
	public function get_primary_key() {
		return $this->{static::get_the_primary_key()};
	}

	/**
	 * Get all of the properties of this model as an array
	 * @return array
	 */
	public function to_array() {
		return $this->properties();
	}

	/**
	 * Return an array of all the properties for this model
	 * @return array
	 */
	public function properties() {
		return get_object_vars( $this );
	}

	/**
	 * Create a new model
	 *
	 * @param $properties
	 *
	 * @return static
	 */
	public static function create( $properties ) {
		return new static( $properties );
	}

	/**
	 * Save this model to the database
	 * @return int
	 */
	public function save() {
		global $wpdb;

		$properties = $this->properties();

		/**
		 * Insert
		 * to wpdb
		 */
		$wpdb->insert( $this->get_table(), $properties );
		$this->{static::get_the_primary_key()} = $wpdb->insert_id;

		return $this->{static::get_the_primary_key()};
	}

	/**
	 * Find a specific model by id
	 *
	 * @param int $id
	 *
	 * @return false|self
	 */
	public static function find( $id ) {
		global $wpdb;

		$id = absint( $id );

		$properties = $wpdb->get_row( "SELECT * FROM " . static::get_table() . " WHERE " . static::get_the_primary_key() . " = '" . $id . "'", ARRAY_A );

		/** check properties */
		if ( ! $properties ) {
			return false;
		}

		return static::create( $properties );
	}

	/**
	 * Get the table used to store email log
	 * @return string
	 */
	public static function get_table() {
		global $wpdb;

		return $wpdb->prefix . 'lana_email_logger_logs';
	}

	/**
	 * Get the column used as the primary key
	 * @return string
	 */
	public static function get_the_primary_key() {
		return 'id';
	}
}