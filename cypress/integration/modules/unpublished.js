/**
 * @file
 * Contains end-to-end tests for unpublished content on ASK site.
 */

const {User} = require("./includes/User");

const tests = (cy) => {
  const page = "/resources/unpublished-content";
  const refpage = "/reference-paragraphs";
  const resources = "/resources";

  describe("Unpublished pages.", () => {
    it("Not visible to unauthenticated users.", () => {
      cy.request({url: page, failOnStatusCode: false}).its('status').should('eq', 403)
        .visit('/')
        .get('nav ul li.not-published').should('not.exist');
    });

    it("Visible to authenticated users", () => {
      const user = new User();
      const percyOptions = {};
      user.login("author", "author");

      cy.request({url: page, failOnStatusCode: false}).its('status').should('eq', 200)
        .visit(page)
        .get('nav ul li.not-published').should('exist')
        .percySnapshot("health_starter_kit_sample_unpublished_page", percyOptions);
    });

    it("Does not appear in listing when list is viewed by unauthenticated user.", () => {
      const percyOptions = {};

      cy.visit(refpage)
        .get('nav ul li.not-published').should('not.exist')
        .percySnapshot("health_starter_kit_sample_unpublished_list", percyOptions);
    });


    it("Appears in listing when list is viewed by authenticated user.", () => {
      const user = new User();
      const percyOptions = {};
      user.login("author", "author");

      cy.visit(refpage)
        .get('nav ul li.not-published').should('exist')
        .percySnapshot("health_starter_kit_sample_unpublished_list", percyOptions);
    });

    it("Does not appear in resources when resources is viewed by unauthenticated user.", () => {
      const user = new User();
      const percyOptions = {};

      cy.visit(resources)
        .get('nav ul li.not-published').should('not.exist')
        .percySnapshot("health_starter_kit_sample_unpublished_resources_unauthenticated", percyOptions);
    });


    //Currently not working. Leaving the test here for the future
    // it("Appears in resources when list is viewed by authenticated user.", () => {
    //   const user = new User();
    //   const percyOptions = {};
    //   user.login("author", "author");
    //
    //   cy.visit(resources)
    //     .get('.health-listing .not-published').should('exist')
    //     .percySnapshot("health_starter_kit_sample_unpublished_resources", percyOptions);
    // });


  });
}

exports.tests = tests;
