<?php
// Turn off PHP errors 
error_reporting(0);

// Team Class - Gets additional information about the team
class Team {

    // Invoke __construct function to initiate process
    public function __construct(){
        Team::Data();
    }

    // Invoke function to handle getting data, checking existing data and updating data
    public function Data(){

        // Get data from json file
        $json_data = json_decode(file_get_contents('data/teams.json'));

        // Make variables/arrays for team data
        $teams = ['Team1','Team2','Team3','Team4'];
        $team_count = 0;

        // Loop through the json data checking the whether the team name is empty or not
        /*
            * if the team name is empty it will not count that as a team
            * if the team name is not empty that will count as a team slot fulfilled 
        */
        $registered_teams = [];
        foreach ($teams as $team){
            $single_team = $json_data->$team->{"TeamName"};
            if(!empty($single_team)){
                $team_count++;
                array_push($registered_teams, $single_team);
            }
        }

        // Check that the amount of teams is not equal to or greater than the maximum amount of teams
        if ($team_count >= 4){
            exit("4 teams have already entered the competition");
        }

        // Get input for team name
        $team_name = readline("Enter your team name: ");
        
        // Validate teamname- if team name is empty throw an error
        if (empty(trim($team_name))){
            exit("Your team name can't be empty.");
        }

        // Count the amount of registered teams 
        foreach ($registered_teams as $teams){ 
            $count = 0;
            if ($registered_teams[$teams] == $team_name){
                $count++;
            }
        }

        // Check if team name is taken
        if ($count > 0){ 
            exit("A team with that name has already been registered.");
        } 
        
        // Get input for team members
        $team_members = readline("Enter the names of your team members, separated by commars: ");

        // Split team members from string into an array and validate data
        $team_members = explode(",",$team_members);
        $count = 0;
        $length = count($team_members);
        if ($length != 5 ){
            exit("Your team does not have enough members.");
        }
        while ($count < $length){
            trim($team_members[$count]);
            $count++;
        }
        

        // Enter data into json file and show success message
        $json_data->$team->{"TeamName"} = $team_name;
        $json_data->$team->{"TeamMembers"} = $team_members;
        $rewrite_data = json_encode($json_data);
        file_put_contents('data/teams.json', $rewrite_data);
        echo "Data saved successfully";
    }
}

// Individual Class - Gets additional information about the individual 
class Individual{

    // Invoke __construct function to handle starting the process
    public function __construct(){
        Individual::Data();
    }

    // Invoke function to handle getting data, checking existing data and updating data
    public function Data(){

        // Get data from json file
        $json_data = json_decode(file_get_contents('data/individuals.json'));

        // Assign needed variables
        $competitor_array = $json_data->{"Competitors"};
        $competitor_count = 0;

        // Get individual's name
        $competitor_name = readline("What is your name? ");

        // Check that the name that was inputed isn't empty
        if(empty(trim($competitor_name))){
            exit("Your name input can't be empty");
        }

        // Loop through json array to check whether that name hasn't already been registered
        foreach($competitor_array as $i){
            // Stop loop from being infiniate
            $competitor_count++;

            // If it has been registered throw error
            if ($competitor_name == $competitor_array[$i]){
                exit("Someone has already entered the competiton using that name!");
            }
        }
        
        // If more or equal to 20 individuals have registerd throw an error
        if ($competitor_count >= 20){
            exit("There are no spaces available for registration of an individual. ");
        }

        // Enter data into json and send success message
        array_push($json_data->{"Competitors"},$competitor_name);
        $rewrite_data = json_encode($json_data);
        file_put_contents('data/individuals.json', $rewrite_data);
        echo "Data saved successfully";
    }
}

// Create Scores class to handle saving, getting, checking and modifiying score data
class Scores {

    // Invoke __construct function - this will run the code inside as soon as a new Scores object is made
    public function __construct(){

        // Save the json data to a variable that can be called in other functions inside the class
        $this->json = json_decode(file_get_contents("data/scores.json"));
    }

    // Invoke function to handle individual data
    public function Team(){

        // Get data from json file
        $data = $this->json;

        // Get user input for individual's name
        $team_name = readline("Enter the team name: ");

        // Check that the individual's name is not empty, if it is throw an error
        if (empty(trim($team_name))){
            exit("The team name can't be empty");
        }

        // Assign variables for looping process
        $team_score_count = 0;
        $i = 0;

        // Loop through all of the teams in the scores.json file
        while($i < 4){
            // If the data is entered for the specific team it will add 1 to the team score count
            if (!empty(trim($data->{"Teams"}->{$i}->{"TeamName"})) && !empty(trim($data->{"Teams"}->$i->{"Score"}))){
                $team_score_count++;
            }
            // Progress the loop so there is not an infinate loop
            $i++;
        }

        // Make sure that there are less than 4 team scores registered, if not throw an error
        if ($team_score_count >= 4){
            exit("All 4 teams have had scores registered");
        }

        // Get input for score and assign to score variable
        $score = readline("What is the score for the ".$team_name." team? ");

        // Make sure that the score input is not empty, if it is empty throw an error
        if (empty(trim($score))){
            exit("Score can't be empty");
        }

        // Make sure that the score input is above 0, if it is below 0 throw an error
        if ($score <= 0){
            exit("Score must be greater than 0");
        }
        
        // Enter data into json file and print success message
        $data->{"Teams"}->{$team_score_count+1}->{"TeamName"} = $team_name;
        $data->{"Teams"}->{$team_score_count+1}->{"Score"} = $score;
        $rewrite = json_encode($data);
        file_put_contents("data/scores.json",$rewrite);
        echo "Data saved successfully";

    }

    // Invoke function to handle individual data 
    public function Individual(){
        // Get data from json file
        $data = $this->json;

        // Get user input for individual's name
        $individual_name = readline("Enter individual's name: ");

        // Check that the individual's name is not empty, if it is throw an error
        if (empty(trim($individual_name))){
            exit("Individual name input can't be empty");
        }

        // Assign variables for looping process
        $individual_score_count =0;
        $i = 0;

        // Loop through all of the individual's in the scores.json file
        while ($i < 20){
            // If the data is entered for the specific individual it will add 1 to the individual score count
            if(!empty(trim($data->{"Individuals"}->{$i}->{"IndividualName"})) && !empty(trim($data->{"Individuals"}->{$i}->{"Score"}))){
                $individual_score_count++;
            }
            // Progress the loop so there is not an infinate loop
            $i++;
        }

        // Make sure that there are less than 20 individual scores registered, if not throw an error
        if ($individual_score_count >= 20 ){
            exit("All 20 individual spaces have had scores registered for them");
        }

        // Get input for score and assign to score variable
        $score = readline("What is the score for ".$individual_name."? ");

        // Make sure that the score input is not empty, if it is empty throw an error
        if (empty(trim($score))){
            exit("Score can't be empty");
        }

        // Make sure that the score input is above 0, if it is below 0 throw an error
        if ($score <= 0){
            exit("Score must be greater than 0");
        }

        // Enter data into json file and print success message
        $data->{"Individuals"}->{$individual_score_count+1}->{"IndividualName"} = $individual_name;
        $data->{"Individuals"}->{$individual_score_count+1}->{"Score"} = $score;
        $rewrite = json_encode($data);
        file_put_contents("data/scores.json",$rewrite);
        echo "Data saved successfully";
    }
}

// Print options and get the user input for option
echo "1. Enter the competition as a team or individual. \n2. Input scores for a team or individual\n";
$menu = readline("What would you like to do? ");

// Use a switch to go through the options given by the user input
switch ($menu){
    
    // If the user input is 1 the following code will run
    case 1:

        // Get user input for the next step
        $competition_registration = readline("Would you like to register as a team or individual? ");
        $competition_registration = strtolower($competition_registration);

        // Switch statement for the user input
        switch ($competition_registration){

            // If the user input is team, make a new instance of the Team class
            case "team":
                new Team();
                break;

            // If the user input is individual, make a new instance of the Individual class
            case "individual":
                new Individual();
                break;
            
            // If neither team nor individual is selected throw an error
            default:
                exit("Invalid option selected");
        }
        break;

    // If the user input is 2 the following code will run
    case 2:
        
        // Get user input for the next step
        $score_data = readline("Would you like to enter scores for an individual or team? ");
        $score_data = strtolower($score_data);
        
        // Make a new instance of the Scores class
        $scoring = new Scores();

        // Switch statement for the user input
        switch ($score_data){

            // If the user input is team, call the Team function from the Scores class
            case "team": 
                $scoring->Team();
                break;

            // If the user input is individual, call the Individual function from the Scores class
            case "individual":
                $scoring->Individual();
                break;

            // If niether team nor individual is selected an error will be printed
            default:
                exit("Invalid option selected");
        }
        break;

    // If no valid option is selected the following error will be printed
    default:
        exit("Invalid option selected");
}