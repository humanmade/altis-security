<?php
/**
 * Tests for Altis Security module's audit log.
 *
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

use Codeception\Util\Locator;

/**
 * Tests for Altis Security module's audit log.
 */
class AuditLogCest {

	/**
	 * Create a new post to add the action to the audit log.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testCreatePostForAuditLog( AcceptanceTester $I ) {
		$I->wantToTest( 'Create a new post to add the action to the audit log.' );
		$I->loginAsAdmin();

		// Go to new post page.
		$I->amOnAdminPage( 'post-new.php' );

		// Add a title.
		$I->click( '.editor-post-title__input' );
		$I->type( 'Test audit log' );

		// Publish the post.
		$I->click( '.editor-post-publish-button__button' );
		$I->click( '.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button' );
		$el = Locator::contains( '.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button', 'Publishing' );
		$I->waitForElementNotVisible( $el, 20 );

		// Check post is published correctly.
		$I->seePostInDatabase( [
			'post_title' => 'Test audit log',
			'post_status' => 'publish',
		] );
	}

	/**
	 * Confirm recent actions and filtering in the audit log.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testConfirmActionsInAuditLog( AcceptanceTester $I ) {
		$I->wantToTest( 'I want to view recent actions and filtering in the audit log.' );
		$I->loginAsAdmin();

		// Go to the Audit Log.
		$I->amOnAdminPage( 'network/admin.php?page=wp_stream' );

		// Check if the audit log contains the recently published post.
		$I->see( '"Test audit log" post published' );

		// Set filters for "Today".
		$I->click( '.actions .date-interval' );
		$I->click( '.select2-container--open input.select2-search__field' );
		$I->type( 'Today' );
		$I->click( '.select2-results__option.select2-results__option--highlighted' );

		// Click to apply filters.
		$I->click( '#record-query-submit' );

		// Check if the audit log contains the recently published post.
		$I->see( '"Test audit log" post published' );

		// Fill in the search form.
		$I->click( '#record-search-input' );
		$I->type( 'Test audit log' );
		$I->click( '#search-submit' );

		// Check if the audit log contains the recently published post.
		$I->see( '"Test audit log" post published' );
	}

}
