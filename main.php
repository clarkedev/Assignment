<?php
// Turn off PHP errors 
error_reporting(0);

// Individual competitor information
$individual_events = ['dodge ball', 'spelling bee', '100m sprint', 'laser quest free-for-all', 'hide and seek'];

// Team competitor(s) information
$team_events = ['5-a-side football', 'team spelling bee', 'relay race', 'team laser quest', 'team dodge ball'];

// Team Class - Gets additional information about the team
class Team {
    // Initiate process
    public function __construct(){
        Team::Data();
    }

    // Invoke function to handle getting data, checking existing data and updating data
    public function Data(){
        // Get data from `json` file
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

        // Get data for team registration

        # TeamName
        $team_name = readline("Enter your team name: ");
        // Validate teamname
        if (empty(trim($team_name))){ # If team name is empty, throw an error 
            exit("Your team name can't be empty.");
        }
        foreach ($registered_teams as $teams){ // Checks whether the team name is taken
            $count = 0;
            if ($registered_teams[$teams] == $team_name){
                $count++;
            }
        }
        if ($count > 0){ # If team name is taken, throw an error 
            exit("A team with that name has already been registered.");
        } 

        # Team Members
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
        

        // Enter data into json file.
        $json_data->$team->{"TeamName"} = $team_name;
        $json_data->$team->{"TeamMembers"} = $team_members;
        $rewrite_data = json_encode($json_data);
        file_put_contents('data/teams.json', $rewrite_data);

    }
}

// Individual Class - Gets additional information about the individual 
class Individual{
    // Initiate process
    public function __construct(){
        Individual::Data();
    }

    // Invoke function to handle getting data, checking existing data and updating data
    public function Data(){
        $json_data = json_decode(file_get_contents('data/individuals.json'));
        $competitor_array = $json_data->{"Competitors"};
        $competitor_count = 0;

        $competitor_name = readline("What is your name? ");
        if(empty(trim($competitor_name))){
            exit("Your name input can't be empty");
        }
        foreach($competitor_array as $i){
            $competitor_count++;
            if ($competitor_name == $competitor_array[$i]){
                exit("Someone has already entered the competiton using that name!");
            }
        }
        echo $competitor_count;
        if ($competitor_count >= 20){
            exit("There are no spaces available for registration of an individual. ");
        }
        array_push($json_data->{"Competitors"},$competitor_name);
        $rewrite_data = json_encode($json_data);
        file_put_contents('data/individuals.json', $rewrite_data);
        
    }
}

class Scores {
    public function __construct(){
        $this->json = json_decode(file_get_contents("data/scores.json"));

    }
    public function Team(){
        $data = $this->json;
        $team_name = readline("Enter the team name: ");
        if (empty(trim($team_name))){
            exit("The team name can't be empty");
        }
        $team_score_count = 0;
        $i = 0;
        while($i < 4){
            if (!empty(trim($data->{"Teams"}->{$i}->{"TeamName"})) && !empty(trim($data->{"Teams"}->$i->{"Score"}))){
                $team_score_count++;
            }
            $i++;
        }
        if ($team_score_count >= 4){
            exit("All 4 teams have had scores registered");
        }
        $score = readline("What is the score for the ".$team_name." team? ");
        if (empty(trim($score))){
            exit("Score can't be empty");
        }
        if ($score <= 0){
            exit("Score must be greater than 0");
        }
        echo $team_score_count;
        // Enter data
        $data->{"Teams"}->{$team_score_count+1}->{"TeamName"} = $team_name;
        $data->{"Teams"}->{$team_score_count+1}->{"Score"} = $score;
        $rewrite = json_encode($data);
        file_put_contents("data/scores.json",$rewrite);
        echo "Data saved successfully";

    }
    public function Individual(){
        $data = $this->json;
        $individual_name = readline("Enter individual's name: ");
        if (empty(trim($individual_name))){
            exit("Individual name input can't be empty");
        }
        $individual_score_count =0;
        $i = 0;
        while ($i < 20){
            if(!empty(trim($data->{"Individuals"}->{$i}->{"IndividualName"})) && !empty(trim($data->{"Individuals"}->{$i}->{"Score"}))){
                $individual_score_count++;
            }
            $i++;
        }
        if ($individual_score_count >= 20 ){
            exit("All 20 individual spaces have had scores registered for them");
        }
        $score = readline("What is the score for ".$individual_name."?");
        if (empty(trim($score))){
            exit("Score can't be empty");
        }
        if ($score <= 0){
            exit("Score must be greater than 0");
        }

        // enter data
        $data->{"Individuals"}->{$individual_score_count+1}->{"IndividualName"} = $individual_name;
        $data->{"Individuals"}->{$individual_score_count+1}->{"Score"} = $score;
        $rewrite = json_encode($data);
        file_put_contents("data/scores.json",$rewrite);
        echo "Data saved successfully";
    }
}
// Menu - get user input for what task to do
echo "1. Enter the competition as a team or individual. \n2. Input scores for a team or individual\n";
$menu = readline("What would you like to do? ");
switch ($menu){
    case 1:
        $competition_registration = readline("Would you like to register as a team or individual? ");
        $competition_registration = strtolower($competition_registration);
        switch ($competition_registration){
            case "team":
                new Team();
                break;
            case "individual":
                new Individual();
                break;
            default:
                exit("Invalid option selected");
        }
        break;
    case 2:
        $score_data = readline("Would you like to enter scores for an individual or team? ");
        $score_data = strtolower($score_data);
        $scoring = new Scores();
        switch ($score_data){
            case "team": 
                $scoring->Team();
                break;
            case "individual":
                $scoring->Individual();
                break;
            default:
                exit("Invalid option selected");
        }
        break;
    default:
        exit("Invalid option selected");
}