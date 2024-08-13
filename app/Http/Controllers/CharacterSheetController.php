<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Features;
use App\Models\Armor;
use App\Models\Perks;
use App\Models\Weapons;

class CharacterSheetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $encoded)
    {
        $character = json_decode(base64_decode($encoded, true), true);
        
        $compendium = array(); //Details of each feature and whatnot.

        $out = array(
            "Name" => $character["CharacterName"], 
            "Description"=>  implode(", ", $character['Description']),
            "Stats" => $character["Stats"],
            "Skills" => $character["Skills"],
            "Expertises" => $character["Expertises"],
            "Treasure" => $character["Treasure"]
        );

        $out["Tier"] = 1;

        $out["Defenses"] = array(
            "Body" => $out["Stats"]["Strength"] + $out["Stats"]["Fortitude"],
            "React" => $out["Stats"]["Dexterity"] + $out["Stats"]["Wits"],
            "Mind" => $out["Stats"]["Aptitude"] + $out["Stats"]["Will"]
        );

        $out["Defenses"]["Deflect"] = $out["Defenses"]["React"];
        $out["Defenses"]["Soak"] = $out["Defenses"]["Body"];
        $out["HP"]["Current"] = $character["BaseHP"];
        $out["HP"]["Base"] = $character["BaseHP"];

        $weapons_model = new Weapons();
        $armor_model = new Armor();
        $perks_model = new Perks();

        $out["Perks"] = array();

        $out["Manuvers"] = ["Stride"=>"Move 6 spaces."];
        $out["Responses"] = ["Movement Response" => "Make a melee attack when a target leaves a threatened square."];

        $weapon_actions = [];
        foreach ($character["Equipment"] as $equip=>$count){
            /**
             * Add Weapon actions
             */
            if (isset($weapons_model->data[$equip])){
                $action = array();
                $action["Title"] = $equip;
                if (in_array("Finesse", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Dexterity"] + $out["Skills"]["Weapons"];
                    $action["Dmg Bonus"] = $out["Stats"]["Dexterity"];
                }else if (in_array("Ranged", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Dexterity"] + $out["Skills"]["Weapons"];
                    $action["Dmg Bonus"] = $out["Stats"]["Dexterity"];
                }else if (in_array("Mechanical", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Wits"] + $out["Skills"]["Weapons"];
                    $action["Dmg Bonus"] = $out["Stats"]["Wits"];
                }else{
                    $action["Bonus"] = $out["Stats"]["Strength"] + $out["Skills"]["Weapons"];
                    $action["Dmg Bonus"] = $out["Stats"]["Strength"];
                }

                $action["Vs"]="Deflect";
                $action["Damage"] = $weapons_model->data[$equip]["Damage"];

                $action["Tags"] = implode(", ", $weapons_model->data[$equip]["Tags"]);

                foreach ($weapons_model->data[$equip]["Tags"] as $tag){
                    if ($perks_model->has($tag)){
                        $out["Perks"][$tag] = $perks_model->data[$tag]["Description"];
                        $compendium[$tag] = $perks_model->data[$tag]["Description"];
                    }
                }
                $out["Actions"][$action['Title']] = $action;
                $weapon_actions[$action['Title']] = $action;
                if (in_array("Fine", $weapons_model->data[$equip]["Tags"])){
                    $out["Manuevers"][$action['Title']] = $action;
                }

                if (in_array("Versatile D8", $weapons_model->data[$equip]["Tags"])){
                    $action["Title"] = $action["Title"] . " Two-Handed";
                    $action["Damage"] = "D8";
                    $out["Actions"][$action['Title']] = $action;
                }
                if (in_array("Versatile D10", $weapons_model->data[$equip]["Tags"])){
                    $action["Title"] = $action["Title"] . " Two-Handed";
                    $action["Damage"] = "D10";
                    $out["Actions"][$action['Title']] = $action;
                    $weapon_actions[$action['title']] = $action;
                }

            }
            /**
             * Manipulate Defenses 
             */
            if (isset($armor_model->data[$equip])){
                
                $armor = $armor_model->data[$equip];
                if(isset($armor["Deflection Bonus"])){
                    $out["Defenses"]["Deflect"] +=$armor["Deflection Bonus"];
                }elseif(isset($armor["Deflect"])){
                    $out["Defenses"]["Deflect"] =$armor["Deflect"];
                }
                $out["Defenses"]["Soak"] += $armor["Soak"];

                if($equip == "Shield"){
                    $manuver = array();
                    $manuver["Title"]="Shield Bash";
                    $manuver["Bonus"]=$out["Stats"]["Strength"] + $out["Skills"]["Weapons"];
                    $manuver["Vs"]="Deflect";
                    $manuver["Damage"]="D6";
                    $manuver["Tags"] = ["Shield", "Slam"];
                    $out["Perks"]["Shield"] = $perks_model->data["Shield"]["Description"];
                    $compendium["Shield"] = $perks_model->data[$tag]["Description"];
                    $out["Perks"]["Slam"] = $perks_model->data["Slam"]["Description"];
                    $compendium["Slam"] = $perks_model->data[$tag]["Description"];
                }
            }
        }
        


        /**
         * Add Feature actions
         */
        $features_model = new Features();
        $out["Features"] = array();
        foreach ($character["Features"] as $feature=>$has){
            if (isset($features_model->data[$feature])){
                $curr_feature = $features_model->data[$feature];
                $compendium[$curr_feature["Title"]] = $curr_feature["Description"];
                if (isset($curr_feature['{{Weapon}}'])){
                    foreach ($weapon_actions as $weapon=>$details) {
                        if (isset($curr_feature["{{Weapon}}"]["Exploit"])){
                            $details["Exploit"] = true;
                            $weapon = $weapon . "-Exploit";
                        }
                        if (isset($curr_feature["Bonus Damage"])){
                            $details["Additional Damage"][] = $curr_feature["Bonus Damage"];
                        }
                        $out["Actions"][$weapon] = $details;
                    }
                    if (isset($curr_feature["Short Description"])){
                        $out["Features"][$curr_feature["Title"]] = $curr_feature["Short Description"];
                    }else{
                        $out["Features"][$curr_feature["Title"]] = "";
                    }
                    
                }elseif(isset($curr_feature["Maneuver"])){
                    dump($curr_feature["Maneuver"]);

                }elseif(isset($curr_feature["Interaction"])){
                    dump($curr_feature["Interaction"]);
                }elseif(isset($curr_feature["Ritual"])){

                }
            }
        }

        dd($character, $out, $compendium);
    }
}
