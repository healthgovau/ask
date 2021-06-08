/**
 * @file
 * Contains end-to-end tests for curated listing on ASK site.
 */

const tests = (cy) => {
  const curatedPageUri = "/star-wars";

  describe("The sample curated listing page", () => {

    it("Contains a curated paragraph", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(curatedPageUri)
        .get(".health-listing .row .au-display-lg a")
        .contains('The Rise of Darth Vader')
        .then(() => {
          cy.percySnapshot("health_starter_kit_sample_curated_listing", percyOptions);
        });
    });
  });

};

exports.tests = tests;
