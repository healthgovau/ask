/**
 * @file
 * Contains end-to-end tests for unpublished content on ASK site.
 */

const {User} = require("./includes/User");

const tests = (cy) => {
  const page = "/resources/unpublished-content";
  const refpage = "/reference-paragraphs";

  describe("The unpublished page.", () => {
    it("Not visible when unauthenticated.", () => {
      cy.request({url: page, failOnStatusCode: false}).its('status').should('eq', 403)
        .visit('/')
        .get('nav ul li.not-published').should('not.exist');
    });

    it("Visible when logged in", () => {
      const user = new User();
      const percyOptions = {};
      user.login("author", "author");

      cy.request({url: page, failOnStatusCode: false}).its('status').should('eq', 200)
        .visit(page)
        .get('nav ul li.not-published').should('exist')
        .percySnapshot("health_starter_kit_sample_unpublished_page", percyOptions);
    });

    it("Appear in list when logged in", () => {
      const user = new User();
      const percyOptions = {};
      user.login("author", "author");

      cy.visit(refpage)
        .get('nav ul li.not-published').should('exist')
        .percySnapshot("health_starter_kit_sample_unpublished_list", percyOptions);
    });


  });
}

exports.tests = tests;
