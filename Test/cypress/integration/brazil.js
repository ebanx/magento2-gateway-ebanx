describe('My First Test', function() {
  it('Visit the store', function() {
    cy.visit('http://localhost/index.php/push-it-messenger-bag.html');

    cy.get('#product-addtocart-button').should('be.visible');

    cy.get('#product-addtocart-button').click();


  })
})
