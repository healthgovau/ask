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

    /**
     * @todo There is currectly a bug in Listing content page functionality.
     * Test has been temporarily disabled until it has been resolved.
     * @see https://trello.com/c/Hs667ytd/323-incorrect-results-count-on-sample-listing-page
     */
     it("renders correctly", () => {
       // Take a snapshot for visual diffing.
       const percyOptions = {};
       cy
         .visit(landingPageUri)
         .scrollTo("bottom")
         .get("img")
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

    /**
     * @todo There is currectly a bug in Listing content page functionality.
     * Test has been temporarily disabled until it has been resolved.
     * @see https://trello.com/c/Hs667ytd/323-incorrect-results-count-on-sample-listing-page
     */
     it("correct result count is displayed", () => {
       cy
         .visit(landingPageUri)
         .get("header")
         .contains("8 results");
     });

    /**
     * @todo There is currectly a bug in Listing content page functionality.
     * Test has been temporarily disabled until it has been resolved.
     * @see https:trello.com/c/Hs667ytd/323-incorrect-results-count-on-sample-listing-page
     */
     it("facets display correctly", () => {
       const facetId = "#facet_h_content_type";

       cy
         .visit(landingPageUri)
         .get(facetId)
         .should("be.visible")
         .contains(facetId, "App or tool (1)")
         .contains(facetId, "Audio (1)")
         .contains(facetId, "Publication (4)")
         .contains(facetId, "Video (1)")
         .contains(facetId, "Webinar (1)");
     });
  });
}

exports.tests = tests;
