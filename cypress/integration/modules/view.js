/**
 * @file
 * Contains end-to-end tests for the View paragraph type.
 */

 const tests = (cy) => {
  const targetPageUri = "/star-wars";

  describe("View paragraph", () => {
    it("visible on page", () => {
      cy
        .visit(targetPageUri)
        .get(".views-view--h-sample-custom-view")
        .should("be.visible")
    });

    it("contains the correct items", () => {
      cy.visit(targetPageUri);
      cy
        .get(".health-embedded-listing-wrapper")
        .eq(0)
        .within(() => {
          cy
            .get("h3")
            .eq(0)
            .contains("A-wing");
          cy
            .get("h3")
            .eq(1)
            .contains("Death Star Plans");
        });
    });
  });
};

exports.tests = tests;
