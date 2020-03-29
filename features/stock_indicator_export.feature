Feature: Stock Indicator Export
  As a member of the marketing team
  In order to be able to review the stock state of the whole product catalog
  I should be able to export stock indicator for all products

  Requirements:
    - The stock indicator export should generate a document with Product Sku - Stock Indicator pairs
    - The stock indicator export can be run for a single product
    - The stock indicator export can be run for a list of products
    - The stock indicator export can be run for the complete catalog

  Business Rules:
    - When the product stock is 0 then it should be exported with a RED indicator
    - When the product stock is greater than 0 but less than or equal to 10 then it should be exported with a YELLOW indicator
    - When the product stock is greater than 10 then it should be exported with a GREEN indicator

  Definitions:
    - Product: An entity which has sku and stock properties.
    - Product Sku: Unique identifier of a product. It is a non-empty string value which cannot contain spaces.
    - Product Stock: An integer value representing how many available of a product. It can be zero or a positive number.
    - Catalog: Represents the collection of all products available in the system.
    - Stock Indicator: A label which describes the state of the product stock. Can be RED, YELLOW or GREEN depending on the available stock.
    - Stock Indicator Export Document: A document containing Stock Indicator Export Document Entries.
    - Stock Indicator Export Document Entry: A Product Sku - Stock Indicator pair describing the stock status of a product.

  Scenario: The stock indicator export can be run for a single product
    Given the catalog contains a product with sku "INVIQA-001"
    When the user runs the stock indicator export for this product
    Then a stock indicator export document is generated
    And the document contains an entry for "INVIQA-001"
    And the document does not have any further entries

  Scenario: The stock indicator export fails if the specified product missing from the Catalog
    Given the catalog does not contain a product with sku "INVIQA-001"
    When the user runs the stock indicator export for "INVIQA-001"
    Then a product not found error for "INVIQA-001" is shown
    And a stock indicator export document is not generated

  Scenario: Out of stock product gets a red stock indicator
    Given the catalog contains a product that has a stock level of "0"
    And the stock indicator export document has been generated for this product
    When the user checks the stock indicator for this product in the document
    Then the user sees a "red" stock indicator

  Scenario Outline: Product with low stock level gets a yellow stock indicator
    Given the catalog contains a product that has a stock level of "<StockLevel>"
    And the stock indicator export document has been generated for this product
    When the user checks the stock indicator for this product in the document
    Then the user sees a "yellow" stock indicator

    Examples:
      | StockLevel |
      | 1          |
      | 5          |
      | 10         |

  Scenario Outline: Product with high stock level gets a green stock indicator
    Given the catalog contains a product that has a stock level of "<StockLevel>"
    And the stock indicator export document has been generated for this product
    When the user checks the stock indicator for this product in the document
    Then the user sees a "green" stock indicator

    Examples:
      | StockLevel |
      | 11         |
      | 20         |

  Scenario: The stock indicator export can be run for a list of products
    Given the catalog contains these products:
      | sku        |
      | INVIQA-001 |
      | INVIQA-002 |
      | INVIQA-003 |
    When the user runs the stock indicator export for these products:
      | sku        |
      | INVIQA-001 |
      | INVIQA-002 |
    Then a stock indicator export document is generated
    And the document contains an entry for "INVIQA-001"
    And the document contains an entry for "INVIQA-002"
    And the document does not have any further entries

  Scenario: The stock indicator export fails if one of the specified products missing from the Catalog
    Given the catalog contains a product with sku "INVIQA-001"
    And the catalog does not contain a product with sku "INVIQA-002"
    When the user runs the stock indicator export for these products:
      | sku        |
      | INVIQA-001 |
      | INVIQA-002 |
    Then a product not found error for "INVIQA-002" is shown
    And a stock indicator export document is not generated

  Scenario: The stock indicator export can be run for the complete catalog
    Given the catalog contains only these products:
      | sku        |
      | INVIQA-001 |
      | INVIQA-002 |
      | INVIQA-003 |
    When the user runs the stock indicator export for the complete catalog
    Then a stock indicator export document is generated
    And the document contains exactly one entry for each product in the catalog
    And the document does not have any further entries
