/**
 * @file
 * Contains end-to-end tests for landing page content on HSK site.
 */

const tests = (cy) => {
  const landingPageUri = "/resources";

  describe("The sample Listing content page", () => {
    it("successfully loads", () => {
      cy.visit(landingPageUri);
    });

    it("renders correctly", () => {
      // Take a snapshot for visual diffing.
      const percyOptions = {};
      cy
        .visit(landingPageUri)
        .scrollTo("bottom")
        .get(".image img")
        .each(($image, index, $images) => {
          cy
            .wrap($image)
            .scrollIntoView()
            .should("be.visible")
        })
        .then(() => {
          cy.percySnapshot("health_starter_kit_sample_listing_page", percyOptions);
        });
    });

    it("correct result count is displayed", () => {
      cy
        .visit(landingPageUri)
        .get("header")
        .contains("6 results");
    });

    it("facets display correctly", () => {
      const facetId = "#facet_h_content_type";

      cy
        .visit(landingPageUri)
        .get(facetId)
        .should("be.visible")
        .contains(facetId, "App or tool (1)")
        .contains(facetId, "Publication (4)")
        .contains(facetId, "Video (1)");
    });
  });
}

exports.tests = tests;
