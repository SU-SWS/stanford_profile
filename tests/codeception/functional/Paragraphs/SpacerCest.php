<?php

/**
 * Class SpacerCest.
 *
 * @group paragraphs
 * @group spacer
 */
class SpacerCest {

    /**
     * The spacer paragraph has one custom field, to set the size of the bottom margin.
     *
     */

    public function testSpacerParagraph(FunctionalTester $I){

        $paragraph = $I->createEntity([
            'type' => 'stanford_spacer',
            'su_spacer_size' => 'su_spacer_default',
            ], 'paragraph');

        $page = $I->createEntity([
            'type' => 'stanford_page',
            'title' => $this->faker->text(30),
            'su_page_components' => [
              'target_id' => $paragraph->id(),
              'entity' => $paragraph,
            ],
        ]);

        $I->logInWithRole('contributor');
        $I->amOnPage($page->toUrl()->toString());
        $I->seeElementInDOM('.paragraph--type--stanford-spacer');
        $I->amOnPage($page->toUrl('edit-form')->toString());

    }

}
