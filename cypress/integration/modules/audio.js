/**
 * @file
 * Contains end-to-end tests for Video content on HSK site.
 */

const tests = (cy) => {
  const page = "/resources/theme-song";

  describe("The sample audio content page", () => {
    it("successfully loads", () => {
      cy.visit(page);
    });

    it("renders correctly", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(page)
        .scrollTo("bottom")
        .get(".health-field audio")
        .should("be.visible")
        .percySnapshot("health_starter_kit_sample_audio_page", percyOptions);
    });
  });
}

exports.tests = tests;
