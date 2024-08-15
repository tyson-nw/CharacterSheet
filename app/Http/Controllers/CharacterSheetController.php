<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Features;
use App\Models\Armor;
use App\Models\Perks;
use App\Models\Weapons;
use App\Models\Spells;

class CharacterSheetController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $encoded)
    {
        $character = json_decode(base64_decode($encoded, true), true);
        
        $out["Compendium"] = array(); //Details of each feature and whatnot.

        //dump($character);

        $out = array(
            "Name" => $character["CharacterName"], 
            "Description"=>  implode(", ", $character['Description']),
            "Stats" => $character["Stats"],
            "Skills" => $character["Skills"],
            "Expertises" => $character["Expertises"],
            "Treasure" => $character["Treasure"],
        );

        if(isset($out["Expertises"]["Bows"])){
            unset($out["Expertises"]["Bows"]);
            $out["Expertises"]["Longbow"] = true;
            $out["Expertises"]["Shortbow"] = true;
        }

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
        $spells_model = new Spells();

        $out["Spellcasting Feature"] = null;
        $out["Perks"] = array();

        $out["Maneuvers"] = ["Stride"=>"Move 6 spaces."];
        $out["Responses"] = ["Movement Response" => "Make a melee attack when a target leaves a threatened square."];

        $weapon_actions = [];
        foreach ($character["Equipment"] as $equip=>$count){
            
            if (isset($weapons_model->data[$equip])){
                $action = array();
                $action["Title"] = $equip;

                $skill_bonus = 0;
                if(isset($out["Skills"]["Weapons"])){
                    $skill_bonus = $out["Skills"]["Weapons"];
                }
                $expertise_bonus = 0;
                if(isset($out["Expertises"][$equip])){
                    $expertise_bonus = 2;
                }

                if (in_array("Finesse", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Dexterity"] + $skill_bonus + $expertise_bonus;
                    $action["Dmg Bonus"] = $out["Stats"]["Dexterity"];
                }else if (in_array("Ranged", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Dexterity"] + $skill_bonus + $expertise_bonus;
                    $action["Dmg Bonus"] = $out["Stats"]["Dexterity"];
                }else if (in_array("Mechanical", $weapons_model->data[$equip]["Tags"])){
                    $action["Bonus"] = $out["Stats"]["Wits"] + $skill_bonus + $expertise_bonus;
                    $action["Dmg Bonus"] = $out["Stats"]["Wits"];
                }else{
                    $action["Bonus"] = $out["Stats"]["Strength"] + $skill_bonus + $expertise_bonus;
                    $action["Dmg Bonus"] = $out["Stats"]["Strength"];
                }

                $action["Vs"]="Deflect";
                $action["Damage"] = $weapons_model->data[$equip]["Damage"];

                $action["Tags"] = implode(", ", $weapons_model->data[$equip]["Tags"]);

                foreach ($weapons_model->data[$equip]["Tags"] as $tag){
                    if ($perks_model->has($tag)){
                        $out["Perks"][$tag] = $perks_model->data[$tag]["Description"];
                        $out["Compendium"][$tag] = $perks_model->data[$tag]["Description"];
                    }
                }
                $out["Actions"][$action['Title']] = $action;
                $weapon_actions[$action['Title']] = $action;
                if (in_array("Fine", $weapons_model->data[$equip]["Tags"])){
                    $out["Maneuvers"][$action['Title']] = $action;
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
                    $out["Compendium"]["Shield"] = $perks_model->data[$tag]["Description"];
                    $out["Perks"]["Slam"] = $perks_model->data["Slam"]["Description"];
                    $out["Compendium"]["Slam"] = $perks_model->data[$tag]["Description"];
                }
            }
        }
        


        /**
         * Add Feature actions
         */

        if (isset($character["Subfeatures"])){
            $out["Subfeatures"] = array();
            foreach($character["Subfeatures"] as $title=>$detail){
                $explode = explode("-", $title);
                $out["Subfeatures"][$explode[0]][$explode[1]] = $detail; 
            }
        }
        $features_model = new Features();
        $out["Features"] = array();
        foreach (array_keys($character["Features"]) as $feature){
            if (isset($features_model->data[$feature])){
                $curr_feature = $features_model->data[$feature];
                $out["Compendium"][$curr_feature["Title"]] = $curr_feature["Description"];

                
                /**
                 * Parse Weapon Actions
                 */
                if (isset($curr_feature['{{Weapon}}'])){ //done
                    foreach ($weapon_actions as $weapon=>$details) {
                        if (isset($curr_feature["{{Weapon}}"]["Exploit"])){
                            $details["Exploit"] = true;
                            $weapon = $weapon . "-Exploit";
                        }
                        if (isset($curr_feature["{{Weapon}}"]["Bonus Damage"])){
                            $details["Additional Damage"][] = $curr_feature["{{Weapon}}"]["Bonus Damage"];
                        }
                        $out["Actions"][$weapon] = $details;
                    }
                    if (isset($curr_feature["{{Weapon}}"]["Description"])){
                        $out["Features"][$curr_feature["Title"]] = $curr_feature["{{Weapon}}"]["Description"];
                    }else{
                        $out["Features"][$curr_feature["Title"]] = "";
                    }
                }elseif(isset($curr_feature["Action"])){ //unused      
                }elseif(isset($curr_feature["Maneuver"])){ //done

                    $out["Maneuvers"][$curr_feature["Title"]]["Title"] = $curr_feature["Title"];
                    if(isset($curr_feature["Maneuver"]["Movement"])){
                        $stride = 6;                     
                        if(str_contains($curr_feature["Maneuver"]["Description"], "{{Stride}}")){
                            $out["Maneuvers"][$curr_feature["Title"]] = str_replace("{{Stride}}", $stride, $curr_feature["Maneuver"]["Description"]);
                        }else{
                            $out["Maneuvers"][$curr_feature["Title"]] = $curr_feature["Maneuver"]["Description"];
                        }
                    }else{
                        $title = $feature;
                        if(isset($curr_feature["Title"])){
                            $title = $curr_feature["Title"];
                        }
                        $out["Maneuvers"][$title] = array();
                        if(isset($curr_feature["Maneuver"]["Roll"])){
                            $skill_bonus = 0;
                            if(isset($curr_feature["Maneuver"]["Roll"][1]) && isset($out["Skills"][$curr_feature["Maneuver"]["Roll"][1]])){
                                $skill_bonus = $out["Skills"][$curr_feature["Maneuver"]["Roll"][1]];
                            }
                            $expertise_bonus = 0;
                            if(isset($curr_feature["Maneuver"]["Roll"][2]) && isset($out["Expertises"][$curr_feature["Maneuver"]["Roll"][2]])){
                                $expertise_bonus = 2;
                            }
                            $out["Maneuvers"][$title]["Bonus"] = $out["Stats"][$curr_feature["Maneuver"]["Roll"][0]] + $skill_bonus + $expertise_bonus;                            
                        }
                        if(isset($curr_feature["Maneuver"]["Vs"])){
                            $out["Maneuvers"][$title]["Vs"] = $curr_feature["Maneuver"]["Vs"];
                        }

                        $out["Maneuvers"][$title]["Description"] = $curr_feature["Maneuver"]["Description"];
                        if(str_contains($curr_feature["Maneuver"]["Description"], "{{subfeatures}}")){
                            $curr_feature["Maneuver"]["Description"] = str_replace("{{subfeatures}}", implode(", ", $out["Subfeatures"][$curr_feature["Title"]]), $curr_feature["Maneuver"]["Description"]);
                        }
                    }
                    if(isset($curr_feature["Maneuver"]["Description"])){
                        $out["Features"][$curr_feature["Title"]]["Description"] = $curr_feature["Maneuver"]["Description"];

                    }else{
                        $out["Features"][$curr_feature["Title"]]["Description"] = "";
                    }

                     if(str_contains($out["Features"][$curr_feature["Title"]]["Description"], "{{Stride}}")){
                        $stride = 6;
                        if(isset($character["Features"]["Halfun-SmallStature"]) || isset($character["Features"]["Gnome-SmallStature"])){
                            $stride = 5;
                        }
                        $out["Features"][$curr_feature["Title"]]["Description"] = str_replace("{{Stride}}" , $stride,$out["Features"][$curr_feature["Title"]]["Description"]);
                    }
                    
                    if(str_contains($out["Features"][$curr_feature["Title"]]["Description"], "{{Subfeatures}}")){
                        $out["Features"][$curr_feature["Title"]]["Description"] = str_replace("{{Subfeatures}}" , implode(", ", $out["Subfeatures"][$curr_feature["Title"]] ),$out["Features"][$curr_feature["Title"]]["Description"]);
                    }

                }elseif(isset($curr_feature["Interaction"])){ //done
                    $title = $feature;
                    if (isset($curr_feature["Title"])){
                        $title = $curr_feature["Title"];
                    }
                    $out["Interaction"][$title] = [];
                    
                    if(isset($curr_feature["Interaction"]["Roll"])){
                        $skill_bonus = 0;
                        if(isset($curr_feature["Interaction"]["Roll"][1]) && isset($out["Skills"][$curr_feature["Interaction"]["Roll"][1]])){
                            $skill_bonus = $out["Skills"][$curr_feature["Interaction"]["Roll"][1]];
                        }
                        $expertise_bonus = 0;
                        if(isset($curr_feature["Interaction"]["Roll"][2]) && isset($out["Expertises"][$curr_feature["Interaction"]["Roll"][2]])){
                            $expertise_bonus = 2;
                        }
                        $out["Interaction"][$title]["Bonus"] = $out["Stats"][$curr_feature["Interaction"]["Roll"][0]] + $skill_bonus + $expertise_bonus;                            
                    }
                                          
                    
                    if(isset($curr_feature["Interaction"]["Vs"])){
                        $out["Interaction"][$title]["Vs"] = $curr_feature["Interaction"]["Vs"];
                    }
                    $out["Interaction"][$title]["Description"] = $curr_feature["Interaction"]["Description"];
                    
                    $out["Features"][$title] = "";
                    if (isset($curr_feature["Interaction"]["Description"])){
                        if(isset($curr_feature["Interaction"]["Description"])){
                            if(str_contains($curr_feature["Interaction"]["Description"], "{{Subfeature}}")){
                                $curr_feature["Interaction"]["Description"] = str_replace("{{Subfeature}}", implode(", ", $out["Subfeatures"][$curr_feature["Title"]]), $curr_feature["Maneuver"]["Description"]);
                            }
                            $out["Features"][$curr_feature["Title"]] = $curr_feature["Interaction"]["Description"];
    
                        }
                        $out["Features"][$title] = $curr_feature["Interaction"]["Description"];
                    }

                }elseif(isset($curr_feature["Ritual"])){ //done
                    $out["Rituals"][$feature] = $curr_feature["Ritual"]["Description"];
                }elseif(isset($curr_feature["Stride"])){ //done
                    $title = $feature;
                    if(isset($curr_feature["Title"])){
                        $title = $curr_feature["Title"];
                    }
                    $out["Maneuvers"]["Stride"] = $curr_feature["Stride"];
                }elseif(isset($curr_feature["Enigma"])){ //done
                    $out["Spellcasting Feature"] = $curr_feature["Enigma"];
                    $out["Spellcasting Bonus"] = $out["Stats"][$curr_feature["Enigma"]["Spellcasting Bonus"][0]] + $out["Skills"][$curr_feature["Enigma"]["Spellcasting Bonus"][1]];

                    $out["Features"][$curr_feature["Title"]]["Spellcasting Bonus"] = strval($out["Spellcasting Bonus"]);
                    if(isset($curr_feature["Spellcasting Feature"]["Base Burn"])){
                        $out["Features"][$curr_feature["Title"]]["Base Burn"] = $curr_feature["Spellcasting Feature"]["Base Burn"];
                        $out["Burn"] = array("Base Burn"=>$curr_feature["Spellcasting Feature"]["Base Burn"], "Current Burn"=>$curr_feature["Spellcasting Feature"]["Base Burn"]);
                    }

                    if(isset($curr_feature["Spellcasting Feature"]["Fervor"])){
                        $out["Fervor"] = 0;
                    }

                    if(isset($curr_feature["Spellcasting Feature"]["Cantrips"])){
                        $out["Features"][$curr_feature["Title"]]["Cantrips"] = $out["Spellcasting Feature"]["Cantrips"];
                    }

                    $out["Features"][$curr_feature["Title"]]["Prepare"] = strval($out["Stats"][$out["Spellcasting Feature"]["Prepare"][0]] + $out["Skills"][$out["Spellcasting Feature"]["Prepare"][1]]);
                    $out["Features"][$curr_feature["Title"]]["To Prepare"] = $out["Spellcasting Feature"]["To Prepare"];

                    $out["Features"][$curr_feature["Title"]]["Max Tier"] = $out["Spellcasting Feature"]["Max Tier"];
                    
                }else{
                    if(isset($curr_feature["Description"])){
                        $out["Features"][$curr_feature["Title"]]["Description"] = $curr_feature["Description"];
                    }
                }

                if(isset($curr_feature["Bonus"])){
                    foreach($curr_feature["Bonus"] as $key=>$bonus){
                        $out["Bonuses"][$key] = $bonus;
                    }
                    
                }
            }
        }


        /**
         * Add Spells
         */

        if(isset($character["Spells"])){
            $out["Spells"] = array();

            foreach ($character["Spells"] as $spell_name=>$value){
                
                if ($spells_model->has($spell_name)){
                    $spell_details = $spells_model->data[$spell_name];
                    
                    $tier = "";
                    if($spell_details["Tier"] == "Cantrip"){
                        $tier = "Cantrips";
                        $out["Spells"][$tier][$spell_name] = $spell_details;    
                    }else{ 
                        $tier = "Tier " . $spell_details["Tier"];
                        $out["Spells"][$tier][$spell_name] = $spell_details;
                    }
                                        
                    $casting_time = $spell_details["Casting Time"] . "s";
                    $out[$casting_time][$spell_name] = array();
                    $out[$casting_time][$spell_name]["isSpell"] = true;
                    $out[$casting_time][$spell_name]['Title'] = $spell_name;
                    $out[$casting_time][$spell_name]['Bonus'] = $out["Spellcasting Bonus"];
                    if(isset($spell_details["Defense"])){
                        $out[$casting_time][$spell_name]['Vs'] = $spell_details["Defense"];
                    }elseif(isset($out["Burn"])){
                        $out[$casting_time][$spell_name]['Vs'] = "Burn";
                    }

                    $tags = array();
                    if(isset($spell_details["Target"])) {
                        $tags[] = $spell_details["Target"];
                    }
                    if(isset($spell_details["Area"])) {
                        $tags[] = $spell_details["Area"];
                    }
                    $out[$casting_time][$spell_name]['Tags'] = implode(", ", $tags);
                    
                    if(isset($spell_details["Perk"])){
                        $out["Perks"][$spell_name] = $spell_details["Perk"];
                    }
                    
                    $out["Spells"][$tier][$spell_name]["Tags"] = $out[$casting_time][$spell_name]['Tags'];
                    $out[$casting_time][$spell_name]['Description'] = $spell_details["Short Description"];

                    if(isset($spell_details["Ritual"])){
                        $out["Rituals"][$spell_name] = $out[$casting_time][$spell_name];
                    }

                    if(isset($spell_details["Maneuver"])){                        
                        $out["Maneuvers"][$spell_name]["Title"] = $spell_details["Maneuver"]["Title"];
                        if(isset($spell_details["Maneuver"]["Roll"])){
                            $skill_bonus = 0;
                            if(isset($spell_details["Maneuver"]["Roll"][1]) && isset($out["Skills"][$curr_feature["Maneuver"]["Roll"][1]])){
                                $skill_bonus = $out["Skills"][$spell_details["Maneuver"]["Roll"][1]];
                            }
                            $expertise_bonus = 0;
                            if(isset($spell_details["Maneuver"]["Roll"][2]) && isset($out["Expertises"][$curr_feature["Maneuver"]["Roll"][2]])){
                                $expertise_bonus = 2;
                            }
                            
                            if(isset($spell_details["Maneuver"]["Roll"])){
                                $out["Maneuvers"][$spell_name]["Bonus"] = $out["Stats"][$spell_details["Maneuver"]["Roll"][0]] + $skill_bonus + $expertise_bonus;                            
                            }
                        }else{
                            $out["Maneuvers"][$spell_name]["Bonus"] = $out["Spellcasting Bonus"] + $skill_bonus;                            
                        }
                        if(isset($spell_details["Maneuver"]["Vs"])){
                            $out["Maneuvers"][$spell_name]["Vs"] = $spell_details["Maneuver"]["Vs"];
                        }
                        $out["Maneuvers"][$spell_name]["Description"] = $spell_details["Maneuver"]["Description"];
                        
                    }
                }
            }
        }

        ksort($out["Spells"], SORT_STRING);
        return view("character_sheet", ["out"=>$out]);   
    }
}