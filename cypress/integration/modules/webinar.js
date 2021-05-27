/**
 * @file
 * Contains end-to-end tests for Video content on HSK site.
 */

const tests = (cy) => {
  const page = "/resources/webinar-darth-vader";

  describe("The sample webinar content page", () => {
    it("successfully loads", () => {
      cy.visit(page);
    });

    it("renders correctly", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(page)
        .get(".webinar-status li")
        .contains("CLOSED")
        .percySnapshot("health_starter_kit_sample_webinar_page", percyOptions);
    });
  });
}

exports.tests = tests;
