# Coding Challenge: Space Rover

## The Problem
[Problem instructions](problem-instructions.pdf)


## My Solution
The solution was designed with the following concepts in mind: Clean architecture, DDD and SOLID principles.

The application gets its input from a text file and then outputs the result to another text file.

Since we are relying on interfaces, it is possible to switch or change the data type of both the input and the output in the future.

On top of that, there is an API endpoint that returns a JSON response.

There are also some unit and integration tests that cover the important functionalities of the application.


### I. Environment
- PHP: 8.2
- Symfony: 7
- PHPUnit: 9


### II. Setup
#### II.1. Dependencies installation
- composer install

#### II.2. Starting the server
- symfony server:start
- Visit http://localhost:8000/


### III. Testing
#### III.1. Running the tests
- php bin/phpunit


### IV. Examples
#### IV.1. Valid input data
To check how the application handles valid input data, please test the following end point:
- http://localhost:8000/api/rover/navigate/input.txt
#### IV.2. Invalid input data
To check how the application handles invalid input data, please test the following end points:
- http://localhost:8000/api/rover/navigate/empty-input.txt
- http://localhost:8000/api/rover/navigate/invalid-bottom-left-border-input.txt
- http://localhost:8000/api/rover/navigate/invalid-upper-right-border-input.txt
- http://localhost:8000/api/rover/navigate/no-command-keys-input.txt
- http://localhost:8000/api/rover/navigate/invalid-command-key-input.txt
- http://localhost:8000/api/rover/navigate/invalid-orientation-input.txt
#### IV.3. Edge cases
It is possible that the final rover's coordinates could exceed the plateau limit. As a solution, I chose to set the rover's final coordinates to the maximum value of the plateau borders instead of throwing an exception. 

Of course, this behavior could be adjusted later, depending on requirements. 

To check how the application handles these scenarios, please test the following end points:
- http://localhost:8000/api/rover/navigate/final-coordinate-exceed-bottom-left-border-input.txt
- http://localhost:8000/api/rover/navigate/final-coordinate-exceed-upper-right-border-input.txt


### V. Future Improvements
#### V.1. Input file processing
- If the file size grows in the future, loading the whole file in memory will not be wise in terms of performance.
- So it will make sense to process the file line-by-line by using ```fopen()``` and ```fgets()``` insteadof ```file_get_contents()```.
#### V.2. Testing coverage
- Currently, not every service is covered by tests. We can add more tests to cover the rest of them.
#### V.3. Request validation
- In our controller, we can add some request validation to make sure the input file name we are receiving is valid by using a regular expression, for example.
#### V.4. Database
- No database usage was requested for this simple task; however, when the application grows, it will make sense to use one.
#### V.4. DDD
- Currently, all our models are defined as entities; however, we can introduce aggregate roots and value objects as well.

### VI. Feedback
I will be looking forward to your feedback!