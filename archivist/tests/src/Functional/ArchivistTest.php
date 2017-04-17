<?php

namespace Drupal\archivist\Tests;

use Drupal\archivist\Entity\HistoryRecord;
use Drupal\Tests\examples\Functional\ExamplesBrowserTestBase;

/**
 * Tests the basic functions of the Archivist module.
 *
 * @package Drupal\archivist\Tests
 *
 * @ingroup archivist
 *
 * @group archivist
 * @group examples
 */
class ContentEntityExampleTest extends ExamplesBrowserTestBase {

  public static $modules = array('archivist', 'block', 'field_ui');

  /**
   * Basic tests for Archivist.
   */
  public function testContentEntityExample() {
    $assert = $this->assertSession();

    $web_user = $this->drupalCreateUser(array(
      'add history_record entity',
      'edit history_record entity',
      'view history_record entity',
      'delete history_record entity',
      'administer history_record entity',
      'administer archivist_history_record display',
      'administer archivist_history_record fields',
      'administer archivist_history_record form display',
    ));

    // Anonymous User should not see the link to the listing.
    $assert->pageTextNotContains('Archivist: History Record Listing');

    $this->drupalLogin($web_user);

    // Web_user user has the right to view listing.
    $assert->linkExists('Archivist: History Record Listing');

    $this->clickLink('Archivist: History Record Listing');

    // WebUser can add entity content.
    $assert->linkExists('Add History Record');

    $this->clickLink(t('Add History Record'));

    $assert->fieldValueEquals('name[0][value]', '');
    $assert->fieldValueEquals('name[0][value]', '');
    $assert->fieldValueEquals('name[0][value]', '');

    $user_ref = $web_user->name->value . ' (' . $web_user->id() . ')';
    $assert->fieldValueEquals('user_id[0][target_id]', $user_ref);

    // Post content, save an instance. Go back to list after saving.
    $edit = array(
      'name[0][value]' => 'test name',
      'first_name[0][value]' => 'test first name',
      'gender' => 'male',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Entity listed.
    $assert->linkExists('Edit');
    $assert->linkExists('Delete');

    $this->clickLink('test name');

    // Entity shown.
    $assert->pageTextContains('test name');
    $assert->pageTextContains('test first name');
    $assert->pageTextContains('male');
    $assert->linkExists('Add History Record');
    $assert->linkExists('Edit');
    $assert->linkExists('Delete');

    // Delete the entity.
    $this->clickLink('Delete');

    // Confirm deletion.
    $assert->linkExists('Cancel');
    $this->drupalPostForm(NULL, array(), 'Delete');

    // Back to list, must be empty.
    $assert->pageTextNotContains('test name');

    // Settings page.
    $this->drupalGet('admin/structure/archivist_history_record_settings');
    $assert->pageTextContains('History Record Settings');

    // Make sure the field manipulation links are available.
    $assert->linkExists('Settings');
    $assert->linkExists('Manage fields');
    $assert->linkExists('Manage form display');
    $assert->linkExists('Manage display');
  }

  /**
   * Test all paths exposed by the module, by permission.
   */
  public function testPaths() {
    $assert = $this->assertSession();

    // Generate a history_record so that we can test the paths against it.
    $history_record = History Record::create(
      array(
        'name' => 'somename',
        'first_name' => 'Joe',
        'gender' => 'female',
      )
    );
    $history_record->save();

    // Gather the test data.
    $data = $this->providerTestPaths($history_record->id());

    // Run the tests.
    foreach ($data as $datum) {
      // drupalCreateUser() doesn't know what to do with an empty permission
      // array, so we help it out.
      if ($datum[2]) {
        $user = $this->drupalCreateUser(array($datum[2]));
        $this->drupalLogin($user);
      }
      else {
        $user = $this->drupalCreateUser();
        $this->drupalLogin($user);
      }
      $this->drupalGet($datum[1]);
      $assert->statusCodeEquals($datum[0]);
    }
  }

  /**
   * Data provider for testPaths.
   *
   * @param int $history_record_id
   *   The id of an existing History Record entity.
   *
   * @return array
   *   Nested array of testing data. Arranged like this:
   *   - Expected response code.
   *   - Path to request.
   *   - Permission for the user.
   */
  protected function providerTestPaths($history_record_id) {
    return array(
      array(
        200,
        '/archivist_history_record/' . $history_record_id,
        'view history_record entity',
      ),
      array(
        403,
        '/archivist_history_record/' . $history_record_id,
        '',
      ),
      array(
        200,
        '/archivist_history_record/list',
        'view history_record entity',
      ),
      array(
        403,
        '/archivist_history_record/list',
        '',
      ),
      array(
        200,
        '/archivist_history_record/add',
        'add history_record entity',
      ),
      array(
        403,
        '/archivist_history_record/add',
        '',
      ),
      array(
        200,
        '/archivist_history_record/' . $history_record_id . '/edit',
        'edit history_record entity',
      ),
      array(
        403,
        '/archivist_history_record/' . $history_record_id . '/edit',
        '',
      ),
      array(
        200,
        '/history_record/' . $history_record_id . '/delete',
        'delete history_record entity',
      ),
      array(
        403,
        '/history_record/' . $history_record_id . '/delete',
        '',
      ),
      array(
        200,
        'admin/structure/archivist_history_record_settings',
        'administer history_record entity',
      ),
      array(
        403,
        'admin/structure/archivist_history_record_settings',
        '',
      ),
    );
  }

  /**
   * Test add new fields to the history_record entity.
   */
  public function testAddFields() {
    $web_user = $this->drupalCreateUser(array(
      'administer history_record entity',
      'administer archivist_history_record display',
      'administer archivist_history_record fields',
      'administer archivist_history_record form display',
    ));

    $this->drupalLogin($web_user);
    $entity_name = 'archivist_history_record';
    $add_field_url = 'admin/structure/' . $entity_name . '_settings/fields/add-field';
    $this->drupalGet($add_field_url);
    $field_name = 'test_name';
    $edit = array(
      'new_storage_type' => 'list_string',
      'label' => 'test name',
      'field_name' => $field_name,
    );

    $this->drupalPostForm(NULL, $edit, t('Save and continue'));
    $expected_path = $this->buildUrl('admin/structure/' . $entity_name . '_settings/fields/' . $entity_name . '.' . $entity_name . '.field_' . $field_name . '/storage');

    // Fetch url without query parameters.
    $current_path = strtok($this->getUrl(), '?');
    $this->assertEquals($expected_path, $current_path);
  }

}
