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

  Rules:
  - When the product stock is 0 then it should be exported with a Red indicator
  - When the product stock is greater than 0 but less than 10 then it should be exported with a Yellow indicator
  - When the product stock is 10 then it should be exported with a Yellow indicator
  - When the product stock is greater than 10 then it should be exported with a Green indicator
  - The stock indicator export can be run for a single product
  - The stock indicator export can be run for all products

  Scenario: Out of stock product gets the red stock indicator
    Given there is a product in the catalog that has a stock level of 0
    When I export the stock indicator for this product
    Then this product should get a red stock indicator

  Scenario Outline: Product with low stock level gets the yellow stock indicator
    Given there is a product in the catalog that has a stock level of <StockLevel>
    When I export the stock indicator for this product
    Then this product should get a yellow stock indicator
    Examples:
      | StockLevel |
      | 1          |
      | 5          |
      | 9          |
      | 10         |

  Scenario Outline: Product with high stock level gets the green stock indicator
    Given there is a product in the catalog that has a stock level of <StockLevel>
    When I export the stock indicator for this product
    Then this product should get a green stock indicator
    Examples:
      | StockLevel |
      | 11         |
      | 20         |

  Scenario: Exporting stock indicator for multiple products
    Given there is a product with sku INVIQA-001 in the catalog that has a stock level of 0
    Given there is a product with sku INVIQA-002 in the catalog that has a stock level of 5
    Given there is a product with sku INVIQA-003 in the catalog that has a stock level of 20
    And there are no other products in the catalog
    When I export the stock indicator for the whole catalog
    Then the INVIQA-001 product should get a red stock indicator
    And the INVIQA-002 product should get a yellow stock indicator
    And the INVIQA-003 product should get a green stock indicator
