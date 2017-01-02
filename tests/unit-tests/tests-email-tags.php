<?php

/**
 * @group email_tags
 */
class Tests_Email_Tags extends Give_Unit_Test_Case {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test function give_email_tag_first_name
	 *
	 * @since 1.9
	 * @cover give_email_tag_first_name
	 */
	function test_give_email_tag_first_name() {
		/*
		 * Case 1: First name from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$firstname  = give_email_tag_first_name( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'Admin', $firstname );

		/*
		 * Case 2: First name from user_id.
		 */
		$firstname = give_email_tag_first_name( array( 'user_id' => 1 ) );
		$this->assertEquals( 'Admin', $firstname );

		/*
		 * Case 3: First name with filter
		 */
		add_filter( 'give_email_tag_first_name', array( $this, 'give_first_name' ), 10, 2 );

		$firstname = give_email_tag_first_name( array( 'donor_id' => 1 ) );
		$this->assertEquals( 'Give', $firstname );

		remove_filter( 'give_email_tag_first_name', array( $this, 'give_first_name' ), 10 );
	}

	/**
	 * Add give_email_tag_first_name filter to give_email_tag_first_name function.
	 *
	 * @since 1.9
	 *
	 * @param string $firstname
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_first_name( $firstname, $tag_args ) {
		if ( array_key_exists( 'donor_id', $tag_args ) ) {
			$firstname = 'Give';
		}

		return $firstname;
	}

	/**
	 * Test function give_email_tag_fullname
	 *
	 * @since 1.9
	 * @cover give_email_tag_fullname
	 */
	function test_give_email_tag_fullname() {
		/*
		 * Case 1: Full name from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$fullname   = give_email_tag_fullname( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'Admin User', $fullname );

		/*
		 * Case 2: Full name from user_id.
		 */
		$fullname = give_email_tag_fullname( array( 'user_id' => 1 ) );
		$this->assertEquals( 'Admin User', $fullname );

		/*
		 * Case 3: Full name with filter
		 */
		add_filter( 'give_email_tag_fullname', array( $this, 'give_fullname' ), 10, 2 );

		$fullname = give_email_tag_fullname( array( 'donor_id' => 1 ) );
		$this->assertEquals( 'Give WP', $fullname );

		remove_filter( 'give_email_tag_fullname', array( $this, 'give_fullname' ), 10 );
	}

	/**
	 * Add give_email_tag_fullname filter to give_email_tag_fullname function.
	 *
	 * @since 1.9
	 *
	 * @param string $fullname
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_fullname( $fullname, $tag_args ) {
		if ( array_key_exists( 'donor_id', $tag_args ) ) {
			$fullname = 'Give WP';
		}

		return $fullname;
	}

	/**
	 * Test function give_email_tag_username
	 *
	 * @since 1.9
	 * @cover give_email_tag_username
	 */
	function test_give_email_tag_username() {
		/*
		 * Case 1: User name from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$username   = give_email_tag_username( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'admin', $username );

		/*
		 * Case 2: User name from user_id.
		 */
		$username = give_email_tag_username( array( 'user_id' => 1 ) );
		$this->assertEquals( 'admin', $username );

		/*
		 * Case 3: User name with filter
		 */
		add_filter( 'give_email_tag_username', array( $this, 'give_username' ), 10, 2 );

		$username = give_email_tag_username( array( 'donor_id' => 1 ) );
		$this->assertEquals( 'give', $username );

		remove_filter( 'give_email_tag_username', array( $this, 'give_username' ), 10 );
	}

	/**
	 * Add give_email_tag_username filter to give_email_tag_username function.
	 *
	 * @since 1.9
	 *
	 * @param string $username
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_username( $username, $tag_args ) {
		if ( array_key_exists( 'donor_id', $tag_args ) ) {
			$username = 'give';
		}

		return $username;
	}

	/**
	 * Test function give_email_tag_user_email
	 *
	 * @since 1.9
	 * @cover give_email_tag_user_email
	 */
	function test_give_email_tag_user_email() {
		/*
		 * Case 1: User email from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$user_email = give_email_tag_user_email( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'admin@example.org', $user_email );

		/*
		 * Case 2: User email from user_id.
		 */
		$user_email = give_email_tag_user_email( array( 'user_id' => 1 ) );
		$this->assertEquals( 'admin@example.org', $user_email );

		/*
		 * Case 3: User email with filter
		 */
		add_filter( 'give_email_tag_user_email', array( $this, 'give_user_email' ), 10, 2 );

		$user_email = give_email_tag_user_email( array( 'donor_id' => 1 ) );
		$this->assertEquals( 'give@givewp.com', $user_email );

		remove_filter( 'give_email_tag_user_email', array( $this, 'give_user_email' ), 10 );
	}

	/**
	 * Add give_email_tag_user_email filter to give_email_tag_user_email function.
	 *
	 * @since 1.9
	 *
	 * @param string $user_email
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_user_email( $user_email, $tag_args ) {
		if ( array_key_exists( 'donor_id', $tag_args ) ) {
			$user_email = 'give@givewp.com';
		}

		return $user_email;
	}

	/**
	 * Test function give_email_tag_billing_address
	 *
	 * @since 1.9
	 * @cover give_email_tag_billing_address
	 */
	function test_give_email_tag_billing_address() {
		/*
		 * Case 1: Billing Address from payment.
		 */
		$payment_id      = Give_Helper_Payment::create_simple_payment();
		$billing_address = give_email_tag_billing_address( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( '', trim( str_replace( "\n", '', $billing_address ) ) );

		/*
		 * Case 2: Billing Address with filter
		 */
		add_filter( 'give_email_tag_billing_address', array( $this, 'give_billing_address' ), 10, 2 );

		$billing_address = give_email_tag_billing_address( array( 'user_id' => 1 ) );
		$this->assertEquals( 'San Diego, CA', $billing_address );

		remove_filter( 'give_email_tag_billing_address', array( $this, 'give_billing_address' ), 10 );
	}

	/**
	 * Add give_email_tag_billing_address filter to give_email_tag_billing_address function.
	 *
	 * @since 1.9
	 *
	 * @param string $billing_address
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_billing_address( $billing_address, $tag_args ) {
		if ( array_key_exists( 'user_id', $tag_args ) ) {
			$billing_address = 'San Diego, CA';
		}

		return $billing_address;
	}

	/**
	 * Test function give_email_tag_date
	 *
	 * @since 1.9
	 * @cover give_email_tag_date
	 */
	function test_give_email_tag_date() {
		/*
		 * Case 1: Date from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$date       = give_email_tag_date( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'January 2, 2017', $date );

		/*
		 * Case 2: Date with filter
		 */
		add_filter( 'give_email_tag_date', array( $this, 'give_date' ), 10, 2 );

		$date = give_email_tag_date( array( 'user_id' => 1 ) );
		$this->assertEquals( 'December 7, 2014', $date );

		remove_filter( 'give_email_tag_date', array( $this, 'give_date' ), 10 );
	}

	/**
	 * Add give_email_tag_date filter to give_email_tag_date function.
	 *
	 * @since 1.9
	 *
	 * @param string $date
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_date( $date, $tag_args ) {
		if ( array_key_exists( 'user_id', $tag_args ) ) {
			$date = 'December 7, 2014';
		}

		return $date;
	}

	/**
	 * Test function give_email_tag_amount
	 *
	 * @since 1.9
	 * @cover give_email_tag_amount
	 * @cover give_email_tag_price
	 */
	function test_give_email_tag_amount() {
		/*
		 * Case 1: Amount from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$amount     = give_email_tag_amount( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( '$20.00', $amount );

		/*
		 * Case 2: Amount with filter
		 */
		add_filter( 'give_email_tag_amount', array( $this, 'give_amount' ), 10, 2 );

		$amount = give_email_tag_amount( array( 'user_id' => 1 ) );
		$this->assertEquals( '$30.00', $amount );

		remove_filter( 'give_email_tag_amount', array( $this, 'give_amount' ), 10 );
	}

	/**
	 * Add give_email_tag_amount filter to give_email_tag_amount function.
	 *
	 * @since 1.9
	 *
	 * @param string $amount
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_amount( $amount, $tag_args ) {
		if ( array_key_exists( 'user_id', $tag_args ) ) {
			$amount = '$30.00';
		}

		return $amount;
	}

	/**
	 * Test function give_email_tag_payment_id
	 *
	 * @since 1.9
	 * @cover give_email_tag_payment_id
	 */
	function test_give_email_tag_payment_id() {
		/*
		 * Case 1: Payment ID from payment.
		 */
		$payment_id = Give_Helper_Payment::create_simple_payment();
		$payment_id = give_email_tag_payment_id( array( 'payment_id' => $payment_id ) );

		$this->assertEquals( 'GIVE-1', $payment_id );

		/*
		 * Case 2: Payment ID with filter
		 */
		add_filter( 'give_email_tag_payment_id', array( $this, 'give_payment_id' ), 10, 2 );

		$payment_id = give_email_tag_payment_id( array( 'user_id' => 1 ) );
		$this->assertEquals( 'GIVE-1 [Pending]', $payment_id );

		remove_filter( 'give_email_tag_payment_id', array( $this, 'give_payment_id' ), 10 );
	}

	/**
	 * Add give_email_tag_payment_id filter to give_email_tag_payment_id function.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_id
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_payment_id( $payment_id, $tag_args ) {
		if ( array_key_exists( 'user_id', $tag_args ) ) {
			$payment_id = 'GIVE-1 [Pending]';
		}

		return $payment_id;
	}

	/**
	 * Test function give_email_tag_receipt_id
	 *
	 * @since 1.9
	 * @cover give_email_tag_receipt_id
	 */
	function test_give_email_tag_receipt_id() {
		/*
		 * Case 1: Receipt ID from payment.
		 */
		$receipt_id = Give_Helper_Payment::create_simple_payment();
		$receipt_id = give_email_tag_receipt_id( array( 'receipt_id' => $receipt_id ) );

		$this->assertEquals( '', $receipt_id );

		/*
		 * Case 2: Receipt ID with filter
		 */
		add_filter( 'give_email_tag_receipt_id', array( $this, 'give_receipt_id' ), 10, 2 );

		$receipt_id = give_email_tag_receipt_id( array( 'user_id' => 1 ) );
		$this->assertEquals( 'GIVE-1', $receipt_id );

		remove_filter( 'give_email_tag_receipt_id', array( $this, 'give_receipt_id' ), 10 );
	}

	/**
	 * Add give_email_tag_receipt_id filter to give_email_tag_receipt_id function.
	 *
	 * @since 1.9
	 *
	 * @param string $receipt_id
	 * @param array  $tag_args
	 *
	 * @return string
	 */
	public function give_receipt_id( $receipt_id, $tag_args ) {
		if ( array_key_exists( 'user_id', $tag_args ) ) {
			$receipt_id = 'GIVE-1';
		}

		return $receipt_id;
	}

	/**
	 * Test function give_email_tag_donation
	 *
	 * @since 1.9
	 * @cover give_email_tag_donation
	 */
	function test_give_email_tag_donation() {
		/*
		 * Case 1: Donation form title from simple donation.
		 */
		$donation            = Give_Helper_Payment::create_simple_payment();
		$donation_form_title = give_email_tag_donation( array( 'payment_id' => $donation ) );

		$this->assertEquals( 'Test Donation Form', $donation_form_title );

		/*
		 * Case 2: Donation form title from multi type donation.
		 */
		$donation            = Give_Helper_Payment::create_multilevel_payment();
		$donation_form_title = give_email_tag_donation( array( 'payment_id' => $donation ) );

		$this->assertEquals( 'Multi-level Test Donation Form - Mid-size Gift', $donation_form_title );

		/*
		 * Case 3: Donation form title with filter
		 */
		add_filter( 'give_email_tag_donation', array( $this, 'give_donation' ) );
		$donation_form_title = give_email_tag_donation( array( 'payment_id' => $donation ) );
		$this->assertEquals( 'GIVE', $donation_form_title );
		remove_filter( 'give_email_tag_donation', array( $this, 'give_donation' ), 10 );
	}

	/**
	 * Add give_email_tag_donation filter to give_email_tag_donation function.
	 *
	 * @since 1.9
	 *
	 * @param string $donation_form_title
	 *
	 * @return string
	 */
	public function give_donation( $donation_form_title ) {
		$donation_form_title = 'GIVE';

		return $donation_form_title;
	}

	/**
	 * Test function give_email_tag_form_title
	 *
	 * @since 1.9
	 * @cover give_email_tag_form_title
	 */
	function test_give_email_tag_form_title() {
		/*
		 * Case 1: Form title from simple form_title.
		 */
		$payment    = Give_Helper_Payment::create_simple_payment();
		$form_title = give_email_tag_form_title( array( 'payment_id' => $payment ) );

		$this->assertEquals( 'Test Donation Form', $form_title );

		/*
		 * Case 2: Form title from multi type form_title.
		 */
		$payment    = Give_Helper_Payment::create_multilevel_payment();
		$form_title = give_email_tag_form_title( array( 'payment_id' => $payment ) );

		$this->assertEquals( 'Multi-level Test Donation Form', $form_title );

		/*
		 * Case 3: Form title with filter
		 */
		add_filter( 'give_email_tag_form_title', array( $this, 'give_form_title' ) );
		$form_title = give_email_tag_form_title( array( 'payment_id' => $payment ) );
		$this->assertEquals( 'GIVE', $form_title );
		remove_filter( 'give_email_tag_form_title', array( $this, 'give_form_title' ), 10 );
	}

	/**
	 * Add give_email_tag_form_title filter to give_email_tag_form_title function.
	 *
	 * @since 1.9
	 *
	 * @param string $form_title
	 *
	 * @return string
	 */
	public function give_form_title( $form_title ) {
		$form_title = 'GIVE';

		return $form_title;
	}

	/**
	 * Test function give_email_tag_payment_method
	 *
	 * @since 1.9
	 * @cover give_email_tag_payment_method
	 */
	function test_give_email_tag_payment_method() {
		/*
		 * Case 1: Payment method from simple payment_method.
		 */
		$payment        = Give_Helper_Payment::create_simple_payment();
		$payment_method = give_email_tag_payment_method( array( 'payment_id' => $payment ) );

		$this->assertEquals( '', $payment_method );

		/*
		 * Case 2: Payment method with filter
		 */
		add_filter( 'give_email_tag_payment_method', array( $this, 'give_payment_method' ) );
		$payment_method = give_email_tag_payment_method( array( 'payment_id' => $payment ) );
		$this->assertEquals( 'Manual', $payment_method );
		remove_filter( 'give_email_tag_payment_method', array( $this, 'give_payment_method' ), 10 );
	}

	/**
	 * Add give_email_tag_payment_method filter to give_email_tag_payment_method function.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_method
	 *
	 * @return string
	 */
	public function give_payment_method( $payment_method ) {
		$payment_method = 'Manual';

		return $payment_method;
	}

	/**
	 * Test function give_email_tag_payment_total
	 *
	 * @since 1.9
	 * @cover give_email_tag_payment_total
	 */
	function test_give_email_tag_payment_total() {
		/*
		 * Case 1: Payment total from simple payment_total.
		 */
		$payment        = Give_Helper_Payment::create_simple_payment();
		$payment_total = give_email_tag_payment_total( array( 'payment_id' => $payment ) );

		$this->assertEquals( '$20', $payment_total );

		/*
		 * Case 2: Payment total with filter
		 */
		add_filter( 'give_email_tag_payment_total', array( $this, 'give_payment_total' ) );
		$payment_total = give_email_tag_payment_total( array( 'payment_id' => $payment ) );
		$this->assertEquals( '$30', $payment_total );
		remove_filter( 'give_email_tag_payment_total', array( $this, 'give_payment_total' ), 10 );
	}

	/**
	 * Add give_email_tag_payment_total filter to give_email_tag_payment_total function.
	 *
	 * @since 1.9
	 *
	 * @param string $payment_total
	 *
	 * @return string
	 */
	public function give_payment_total( $payment_total ) {
		$payment_total = '$30';

		return $payment_total;
	}

	/**
	 * Test function give_email_tag_sitename
	 *
	 * @since 1.9
	 * @cover give_email_tag_sitename
	 */
	function test_give_email_tag_sitename() {
		/*
		 * Case 1: From WordPress function.
		 */
		$sitename = give_email_tag_sitename();

		$this->assertEquals( 'Test Blog', $sitename );

		/*
		 * Case 2: With filter
		 */
		add_filter( 'give_email_tag_sitename', array( $this, 'give_sitename' ) );
		$sitename = give_email_tag_sitename( );
		$this->assertEquals( 'Test Blog | Give', $sitename );
		remove_filter( 'give_email_tag_sitename', array( $this, 'give_sitename' ), 10 );
	}

	/**
	 * Add give_email_tag_sitename filter to give_email_tag_sitename function.
	 *
	 * @since 1.9
	 *
	 * @param string $sitename
	 *
	 * @return string
	 */
	public function give_sitename( $sitename ) {
		$sitename = 'Test Blog | Give';

		return $sitename;
	}

	/**
	 * Test function give_email_tag_receipt_link_url
	 *
	 * @since 1.9
	 * @cover give_get_receipt_url
	 * @cover give_email_tag_receipt_link_url
	 */
	function test_give_email_tag_receipt_link_url() {
		$payment = Give_Helper_Payment::create_simple_payment();


		$receipt_link_url = give_email_tag_receipt_link_url( array( 'payment_id' => $payment ) );

		$this->assertRegExp(
			'/give_action=view_receipt/',
			$receipt_link_url
		);
	}
}