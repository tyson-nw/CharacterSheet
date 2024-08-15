<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

    </head>



    <body>

        <div class="page">
            <div id="details">
                <div id="name"><strong>{{$out['Name']}}</strong></div>
                <div id="description">{{$out['Description']}}</div>
                <div id="tier">Tier: {{$out['Tier']}}
            </div>
            <div id="personality">
                <div>
                    <strong>Bennies</strong> 
                    <input type="checkbox" id="benny1" /><input type="checkbox" id="benny2" /><input type="checkbox" id="benny3" />
                </div>
            </div>
            <table id="derived">
                <tr>
                    <th>
                        Deflect
                    </th>
                    <th>
                        Soak
                    </th>
                    <th>
                        Temp HP
                    </th>
                    <th>
                        Curr. HP
                    </th>
                    <th>
                        Max HP
                    </th>
                </tr>
                <tr>
                    <td>{{$out["Defenses"]["Deflect"]}}</td>
                    <td>{{$out["Defenses"]["Soak"]}}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>{{$out["HP"]["Base"]}}</td>
                </tr>
            </table>

            <table id="defenses">
                <tr>
                    <th>Body</th>
                    <th>React</th>
                    <th>Mind</th>
                </tr>
                <tr>
                    <td>{{$out["Defenses"]["Body"]}}
                    <td>{{$out["Defenses"]["React"]}}
                    <td>{{$out["Defenses"]["Mind"]}}
                </tr>
            </table>

            <table id="stats">
                <tr>
                    <th>Strength</th>
                    <th>Dexterity</th>
                    <th>Aptitude</th>
                </tr>
                <tr>
                    <td>{{$out["Stats"]["Strength"]}}
                    <td>{{$out["Stats"]["Dexterity"]}}
                    <td>{{$out["Stats"]["Aptitude"]}}
                </tr>
                <tr>
                    <th>Fortitude</th>
                    <th>Wits</th>
                    <th>Will</th>
                </tr>
                <tr>
                    <td>{{$out["Stats"]["Fortitude"]}}
                    <td>{{$out["Stats"]["Wits"]}}
                    <td>{{$out["Stats"]["Will"]}}
                </tr>
            </table>

            <div id="skills">
                <div id="skill_title"><strong>Skills:</strong></div>
                <div id="skill_proficiency">Proficiency: +{{$out['Tier']}}</div>
                <div id="skill_list">
                    {{implode(", ", array_keys($out['Skills']))}}
                </div>
            </div>
            <div id="expertises">
                <div id="expertises_title"><strong>Expertises:</strong></div>
                <div id="expertises_bonus">+2</div>
                <div id="expertises_list">
                    {{implode(", ", array_keys($out['Expertises']))}}
                </div>
            </div>
            <table id="stabilization">
                <tr>
                    <th>Stabilization</th>
                </tr>
                <tr>
                    <td>
                        <input type="checkbox" id="benny1" /><input type="checkbox" id="benny2" /><input type="checkbox" id="benny3" /> Success &nbsp;&nbsp;&nbsp; Failure <input type="checkbox" id="benny1" /><input type="checkbox" id="benny2" /><input type="checkbox" id="benny3" />
                    </td>
                </tr>
            </table>
            <hr/>
          
            @isset($out["Spellcasting Feature"]["Base Burn"])
                <div id="Burn">
                    <div id="CurrBurn"><strong>Current Burn</strong></div>
                    <div id="BaseBurn"><strong>Base Burn</strong> {{$out["Spellcasting Feature"]["Base Burn"]}}</div>
                </div>
            @endisset

            @isset($out["Spellcasting Feature"]["Fervor"])
                <div id="Fervor">
                    <strong>Fervor</strong><div id="CurrentFervor"></div>
                    
                    
                </div>
            @endisset


            <div id="actions">
                <strong>Actions:</strong>
                <ul>
                    @foreach ($out['Actions'] as $action)
                    <li>
                        <strong>{{$action['Title']}}</strong> 
                        
                        @isset($action["Vs"])
                        +{{$action['Bonus']}} vs. {{$action["Vs"]}}@isset($action["Damage"]) ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)@endisset,
                        @endisset

                        @isset($action["Tags"]){{$action["Tags"]}},@endisset
                        @isset($action["Description"]) {{$action["Description"]}}@endisset
                    </li>
                    @endforeach
                    
                </ul>
            </div>
            <div id="maneuvers">
                <strong>Maneuvers:</strong>
                <ul>
                    @foreach ($out['Maneuvers'] as $name=>$action)
                        @if(is_array($action))
                            <li><strong>{{$action["Title"]}}</strong>
                                @isset($action["Vs"])
                                +{{$action['Bonus']}} vs. {{$action["Vs"]}}
                                @endisset
                                @isset($action["Damage"])
                                    ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)
                                @endisset 
                                @isset($action["Tags"]){{$action["Tags"]}},@endisset
                                @isset($action["Description"]) {{$action["Description"]}}@endisset
                            </li>
                        @else
                            <li><strong>{{$name}}</strong> {{$action}}</li>
                        @endif

                    @endforeach
                </ul>
            </div>
            <div id="responses">
                <strong>Responses:</strong>
                <ul>
                    @foreach ($out['Responses'] as $name=>$action)
                        @if(is_array($action))
                            <li><strong>{{$action["Title"]}}</strong>
                                @isset($action["Vs"])
                                +{{$action['Bonus']}} vs. {{$action["Vs"]}}
                                @endisset
                                @isset($action["Damage"])
                                    ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)
                                @endisset 
                                @isset($action["Tags"]){{$action["Tags"]}},@endisset
                                @isset($action["Description"]) {{$action["Description"]}}@endisset
                            </li>
                        @else
                            <li><strong>{{$name}}</strong> {{$action}}</li>
                        @endif

                    @endforeach
                </ul>
            </div>
            @isset($out["Rituals"])
                <div id="rituals">
                    <strong>Rituals:</strong>
                    <ul>
                        @foreach ($out['Rituals'] as $name=>$action)
                            @if(is_array($action))
                                <li><strong>{{$action["Title"]}}</strong>
                                    @isset($action["Vs"])
                                    +{{$action['Bonus']}} vs. {{$action["Vs"]}}
                                    @endisset
                                    @isset($action["Damage"])
                                        ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)
                                    @endisset 
                                    @isset($action["Tags"]){{$action["Tags"]}},@endisset
                                    @isset($action["Description"]) {{$action["Description"]}}@endisset
                                </li>
                            @else
                                <li><strong>{{$name}}</strong> {{$action}}</li>
                            @endif

                        @endforeach
                    </ul>
                </div>
            @endisset
            <div id="perks">
                <strong>Perks:</strong>
                <ul>
                    @foreach($out['Perks'] as $name=>$description)
                        <li><strong>{{$name}}</strong> {{$description}}</li>
                    @endforeach
                </ul>
            </div>

            <div id="Features">
                <strong>Features:</strong>
                <ul>
                    @foreach($out['Features'] as $name=>$feature)
                        <li><strong>{{$name}}</strong>
                        @isset($feature["Spellcasting Bonus"])
                            Spellcasting Bonus: +{{$feature["Spellcasting Bonus"]}} 
                            @isset($feature["Base Burn"])
                                Base Burn: {{$feature["Base Burn"]}}
                            @endisset
                            <br/>
                            @isset($feature["Cantrips"])Cantrips: {{$feature["Cantrips"]}}@endisset
                            @isset($feature["Prepare"])Prepare: {{$feature["Prepare"]}} Max Tier: {{$feature["Max Tier"]}} To Prepare: {{$feature["To Prepare"]}}@endisset
                        @endisset
                        @isset($feature["Description"]){{$feature["Description"]}}@endisset
                    @endforeach                    
                </ul>
        </div>
        <div class="page">
            <h2>Compendium</h2>
            @foreach($out["Compendium"] as $name=>$description)
                <h4>{{$name}}</h4>
                <p>{{$description}}</p>
            @endforeach
        </div>
        @isset ($out["Spells"])
        <div class="page">
            <h2>Spells</h2>
            @php
                
                ksort($out["Spells"], SORT_STRING);
            @endphp
            @foreach($out["Spells"] as $tier => $spells)
                @if($tier == "Cantrip")
                    <h3>Cantrip</h3>
                @endif

                @if($tier == "1")
                    <h3>Tier 1</h3>
                @endif
  
                @foreach($spells as $spell_name=>$spell_details)
                    <h4>{{$spell_name}}</h4>
                    <ul>
                        @if($spell_details["Tier"] == "Cantrip")
                            <li><strong>Tier</strong> Cantrip</li>
                        @elseif($spell_details["Tier"] == "1")
                            <li><strong>Tier</strong> 1</li>
                        @endif
                       
                        <li><strong>Casting Time</strong> {{$spell_details["Casting Time"]}} @isset($spell_details["Ritual"]), Ritual @endisset</li>
                        @isset($spell_details["Tags"]) <li><strong>Target</strong> {{$spell_details["Tags"]}}@isset($spell_details["Defense"]), {{$spell_details["Defense"]}}@endisset @endisset </li>
                        @isset($spell_details["Duration"])<li><strong>Duration</strong> {{$spell_details["Duration"]}}@isset($spell_details["Concentration"]), Concentration @endisset @endisset</li>
                    </ul>
                    <p>{{$spell_details["Description"]}}</p>
                @endforeach 
                         
            @endforeach
        </div>
        @endisset



    </body>
</html>