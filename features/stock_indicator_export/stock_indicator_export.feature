Feature: Stock Indicator Export
  As a member of the marketing team
  In order to be able to review the stock state of the whole product catalog
  I should be able to export a stock indicator for all products

  Definitions:
    - Product: An entity which has sku and stock properties.
    - Product Sku: Unique identifier of a product. It is a non-empty string value which cannot contain spaces.
    - Product Stock: An integer value representing how many available of a product. It can be zero or a positive number.
    - Catalog: Represents the collection of all products available in the system.
    - Stock Indicator: A label which describes the state of the product stock. Can be RED, YELLOW or GREEN depending on the available stock. See Rules below.
    - Stock Indicator Export Document: A document containing Stock Indicator Export Document Entries.
    - Stock Indicator Export Document Entry: A Product Sku - Stock Indicator pair describing the stock status of a product.
    - Product Sku List: A list of Product Skus.
    - Product List: A list of Products.

  Requirements:
    - The stock indicator export can be run for a single product:
    input: Product Sku
    output on success: Stock Indicator Export Document which contains 1 entry with the requested product's stock indicator
    output on error: Specific error message shown which describes the problem and a document should not be generated.
    error cases:
      - product specified by input does not exists
      - infrastructure related issue happened while getting the product from the catalog
      - infrastructure related issue happened while saving the Stock Indicator Export Document

    - The stock indicator export can be run for a list of products:
    input: Product Sku List
    output on success: Stock Indicator Export Document which contains 1 entry for each requested product with the product's stock indicator
    output on error: Specific error message shown which describes the problem and a document should not be generated.
    error cases:
      - some of the product specified by input does not exists
      - infrastructure related issue happened while getting a product from the catalog
      - infrastructure related issue happened while saving the Stock Indicator Export Document

    - The stock indicator export can be run for the catalog:
    input: No input
    output on success: Stock Indicator Export Document which contains 1 entry for each product in the catalog with the product's stock indicator
    output on error: Specific error message shown which describes the problem and a document should not be generated.
    error cases:
    - some of the product specified by input does not exists
    - infrastructure related issue happened while getting a product from the catalog
    - infrastructure related issue happened while saving the Stock Indicator Export Document

  Rules:
    - When the product stock is 0 then it should be exported with a Red indicator
    - When the product stock is greater than 0 but less than 10 then it should be exported with a Yellow indicator
    - When the product stock is 10 then it should be exported with a Yellow indicator
    - When the product stock is greater than 10 then it should be exported with a Green indicator

  Scenario: Stock indicator for out of stock products
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of 0
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for INVIQA-001 with a red stock indicator
    And the document should not have any further entries

  Scenario Outline: Stock indicator for low stock products
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of <StockLevel>
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for INVIQA-001 with a yellow stock indicator
    And the document should not have any further entries
    Examples:
      | StockLevel |
      | 1          |
      | 5          |
      | 9          |
      | 10         |

  Scenario Outline: Exporting stock indicator for a product with high stock availability
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of <StockLevel>
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for INVIQA-001 with a green stock indicator
    And the document should not have any further entries
    Examples:
      | StockLevel |
      | 11         |
      | 20         |

  Scenario: Exporting stock indicators for a filtered list of products
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of 0
    Given there is a product with sku INVIQA-002 in the catalog that has a stock level of 5
    Given there is a product with sku INVIQA-003 in the catalog that has a stock level of 20
    When I run the stock indicator export for INVIQA-001 and INVIQA-003
    Then a stock indicator export document should be generated
    And the document should contain an entry for INVIQA-001 with a red stock indicator
    And the document should contain an entry for INVIQA-003 with a green stock indicator
    And the document should not have any further entries

  Scenario: Exporting stock indicators for the complete catalog
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of 0
    Given there is a product with sku INVIQA-002 in the catalog that has a stock level of 5
    Given there is a product with sku INVIQA-003 in the catalog that has a stock level of 20
    And there are no other products in the catalog
    When I run the stock indicator export for the catalog
    Then a stock indicator export document should be generated
    And the document should contain an entry for INVIQA-001 with a red stock indicator
    And the document should contain an entry for INVIQA-002 with a yellow stock indicator
    And the document should contain an entry for INVIQA-003 with a green stock indicator
    And the document should not have any further entries
