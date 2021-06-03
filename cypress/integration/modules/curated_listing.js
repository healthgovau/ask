/**
 * @file
 * Contains end-to-end tests for curated listing on ASK site.
 */

const tests = (cy) => {
  const curatedPageUri = "/curated-listing";

  describe("The sample curated listing page", () => {
    it("successfully loads", () => {
      cy.visit(curatedPageUri);
    });

    it("renders correctly", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(curatedPageUri)
        .get(".health-listing .row .au-display-lg")

        .then(() => {
          cy.percySnapshot("health_starter_kit_sample_curated_listing", percyOptions);
        });
    });
  });

};

exports.tests = tests;
