<?php
/**
 * Tests for Altis Security module's require-login works.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Tests for Altis Security module's require-login works.
 */
class RequireLoginCest {

	/**
	 * Rollback callback for the require-login activation bootstrap call.
	 *
	 * @var callable
	 */
	protected $rollback = null;

	/**
	 * Make sure the security module's require-login is activated.
	 *
	 * @param AcceptanceTester $I Actor object.
	 *
	 * @return void
	 */
	public function _before( AcceptanceTester $I ) {
		$this->rollback = $I->bootstrapWith( [ __CLASS__, '_enableRequireLogin' ] );
	}

	/**
	 * Confirm when require-login in enable, login form is visible.
	 *
	 * @param AcceptanceTester $I Tester.
	 *
	 * @return void
	 */
	public function testRequireLogin( AcceptanceTester $I ) {
		$I->wantToTest( 'Confirm when require-login in enable, login form is visible.' );
		$I->amOnPage( '/' );

		// I can see the login form.
		$I->seeElement( '#loginform' );
	}

	/**
	 * Activate require-login.
	 *
	 * @return void
	 */
	public static function _enableRequireLogin() {
		add_filter( 'altis.config', function ( array $config ) : array {
			$config['modules']['security']['require-login'] = true;
			return $config;
		} );
	}

}
