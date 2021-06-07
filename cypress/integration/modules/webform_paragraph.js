/**
 * @file
 * Contains end-to-end tests for embedded webform on the ASK site.
 */

const tests = (cy) => {
  const page = "/star-wars";

  describe("Test embedded webform", () => {
    it("request accessible documents webform works correctly", () => {
      cy
        .visit(page)
        .get("textarea[name='request_an_accessible_format']")
        .type("Test message")
        .get("input[name='your_email_address']")
        .type("test@health.gov.au")
        .get("input.health-report-issues-document-usability")
        .click()
        .get("h1")
        .url()
        .should('include', "request-accessible-format/confirmation")
    });
  });
}

exports.tests = tests;
