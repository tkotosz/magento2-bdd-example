Feature: Stock Indicator Export
  As a member of the marketing team
  In order to be able to review the stock state of the whole product catalog
  I should be able to export a stock indicator for all products

  Requirements:
    - The stock indicator export can be run for a single product
    - The stock indicator export can be run for a list of products
    - The stock indicator export can be run for the catalog

  Business Rules:
  - When the product stock is 0 then it should be exported with a Red indicator
  - When the product stock is greater than 0 but less than 10 then it should be exported with a Yellow indicator
  - When the product stock is 10 then it should be exported with a Yellow indicator
  - When the product stock is greater than 10 then it should be exported with a Green indicator

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

  Scenario: Stock indicator for out of stock products
    Given there is a product with sku "INVIQA-001" in the catalog that has a stock level of 0
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for "INVIQA-001" with a red stock indicator
    And the document should not have any further entries

  Scenario Outline: Stock indicator for low stock products
    Given there is a product with sku "INVIQA-001" in the catalog that has a stock level of <StockLevel>
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for "INVIQA-001" with a yellow stock indicator
    And the document should not have any further entries
    Examples:
      | StockLevel |
      | 1          |
      | 5          |
      | 9          |
      | 10         |

  Scenario Outline: Stock indicator for high stock products
    Given there is a product with sku "INVIQA-001" in the catalog that has a stock level of <StockLevel>
    When I run the stock indicator export for that product
    Then a stock indicator export document should be generated
    And the document should contain an entry for "INVIQA-001" with a green stock indicator
    And the document should not have any further entries
    Examples:
      | StockLevel |
      | 11         |
      | 20         |

  Scenario: Exporting stock indicators for a filtered list of products
    Given there is a product with sku "INVIQA-001" in the catalog that has a stock level of 0
    Given there is a product with sku "INVIQA-002" in the catalog that has a stock level of 5
    Given there is a product with sku "INVIQA-003" in the catalog that has a stock level of 20
    When I run the stock indicator export for "INVIQA-001" and "INVIQA-003"
    Then a stock indicator export document should be generated
    And the document should contain an entry for "INVIQA-001" with a red stock indicator
    And the document should contain an entry for "INVIQA-003" with a green stock indicator
    And the document should not have any further entries

  Scenario: Exporting stock indicators for the complete catalog
    Given there is a product with sku "INVIQA-001" in the catalog that has a stock level of 0
    Given there is a product with sku "INVIQA-002" in the catalog that has a stock level of 5
    Given there is a product with sku "INVIQA-003" in the catalog that has a stock level of 20
    And there are no other products in the catalog
    When I run the stock indicator export for the catalog
    Then a stock indicator export document should be generated
    And the document should contain an entry for "INVIQA-001" with a red stock indicator
    And the document should contain an entry for "INVIQA-002" with a yellow stock indicator
    And the document should contain an entry for "INVIQA-003" with a green stock indicator
    And the document should not have any further entries

  Scenario: Exporting stock indicator for non-existing product
    Given the product with sku "INVIQA-001" does not exists in the catalog
    When I run the stock indicator export for that product
    Then I should get an error about that the product does not exists
    And a stock indicator export document should not be generated
