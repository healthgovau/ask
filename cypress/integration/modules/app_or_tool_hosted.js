/**
 * @file
 * Contains end-to-end tests for app or tool content on HSK site.
 */

 const tests = (cy) => {
   const landingPageUri = "/resources/rebel-base-calculator/app";

  describe("The sample App or Tool - Hosted content page", () => {
    it("successfully loads", () => {
      cy.visit(landingPageUri);
    });

    it("renders correctly", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(landingPageUri)
        .percySnapshot("health_starter_kit_sample_app_or_tool_content_page_hosted", percyOptions);
    });
  });
}

exports.tests = tests;
