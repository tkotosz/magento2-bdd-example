Feature: Hello World

  Scenario: Saying Hello World
    Given the user is unknown
    When the user runs the hello world service
    Then a general hello message is shown

  Scenario: Saying Hello World To Bob
    Given the user is called "Bob"
    When the user runs the hello world service
    Then a hello message for "Bob" is shown
