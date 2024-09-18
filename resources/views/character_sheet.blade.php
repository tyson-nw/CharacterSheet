<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <link rel="stylesheet" href="/css/style.css" />
        <link rel="stylesheet" media="screen" href="/css/screen.css" />
        <link rel="stylesheet" media="print" href="/css/print.css" />

        
    </head>

    <body>
        <div class="page">
            <div class="columns">
                <div id="details">
                    <div id="name"><strong>{{$out['Name']}}</strong></div>
                    <div id="tier">Tier: {{$out['Tier']}}</div>
                    <div id="description">{{$out['Description']}}</div>
                    
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
                            Temp<br/>HP
                        </th>
                        <th>
                            Curr.<br/>HP
                        </th>
                        <th>
                            Max<br/>HP
                        </th>
                    </tr>
                    <tr>
                        <td>{{$out["Defenses"]["Deflect"]}} @isset($out["Bonuses"]["Deflect"]){{$out["Bonuses"]["Deflect"]}}@endisset</td>
                        <td>{{$out["Defenses"]["Soak"]}} @isset($out["Bonuses"]["Soak"]){{$out["Bonuses"]["Soak"]}}@endisset</td>
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
                        <td>{{$out["Defenses"]["Body"]}} @isset($out["Bonuses"]["Body"]){{$out["Bonuses"]["Body"]}}@endisset</td>
                        <td>{{$out["Defenses"]["React"]}} @isset($out["Bonuses"]["React"]){{$out["Bonuses"]["React"]}}@endisset</td>
                        <td>{{$out["Defenses"]["Mind"]}} @isset($out["Bonuses"]["Mind"]){{$out["Bonuses"]["Mind"]}}@endisset</td>
                    </tr>
                </table>

                <table id="stats">
                    <tr>
                        <th>Strength</th>
                        <th>Dexterity</th>
                        <th>Aptitude</th>
                    </tr>
                    <tr>
                        <td>{{$out["Stats"]["Strength"]}} @isset($out["Bonuses"]["Strength"]){{$out["Bonuses"]["Strength"]}}@endisset</td>
                        <td>{{$out["Stats"]["Dexterity"]}} @isset($out["Bonuses"]["Dexterity"]){{$out["Bonuses"]["Dexterity"]}}@endisset</td>
                        <td>{{$out["Stats"]["Aptitude"]}} @isset($out["Bonuses"]["Aptitude"]){{$out["Bonuses"]["Aptitude"]}}@endisset</td>
                    </tr>
                    <tr>
                        <th>Fortitude</th>
                        <th>Wits</th>
                        <th>Will</th>
                    </tr>
                    <tr>
                        <td>{{$out["Stats"]["Fortitude"]}} @isset($out["Bonuses"]["Fortitude"]){{$out["Bonuses"]["Fortitude"]}}@endisset</td>
                        <td>{{$out["Stats"]["Wits"]}} @isset($out["Bonuses"]["Wits"]){{$out["Bonuses"]["Wits"]}}@endisset</td>
                        <td>{{$out["Stats"]["Will"]}} @isset($out["Bonuses"]["Will"]){{$out["Bonuses"]["Will"]}}@endisset</td>
                    </tr>
                </table>

                <div id="skills">
                    <div id="skill_title"><strong>Skills:</strong></div>
                    <div id="skill_proficiency">Proficiency: +{{$out['Tier']}}</div>
                    <div id="skill_list">
                        @php
                            foreach(array_keys($out["Skills"]) as $skill){
                                $out["Skills"][$skill] = $skill;
                                if(isset($out["Bonuses"][$skill])){
                                    $out["Skills"][$skill] .= $out["Bonuses"][$skill];
                                }
                            }
                        @endphp
                        {{implode(", ", $out['Skills'])}}
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
                @isset($out["Spellcasting Feature"]["Base Burn"])
                    <div id="Burn" class="section">
                        <div id="CurrBurn"><strong>Current Burn</strong></div>
                        <div id="BaseBurn"><strong>Base Burn</strong> {{$out["Spellcasting Feature"]["Base Burn"]}}</div>
                    </div>
                @endisset

                @isset($out["Spellcasting Feature"]["Fervor"])
                    <div id="Fervor" class="section">
                        <strong>Fervor</strong><div id="CurrentFervor"></div>
                        
                        
                    </div>
                @endisset


                <div id="actions" class="section">
                    <strong>Actions:</strong>
                    <ul>
                        @foreach ($out['Actions'] as $action)
                        <li>
                            <strong>{{$action['Title']}}@isset($action["Exploit"])-Exploit @endisset</strong> 
                            
                            @isset($action["Vs"])
                                +{{$action['Bonus']}} vs. {{$action["Vs"]}}@isset($action["Damage"]) ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)@endisset,
                            @endisset

                            @isset($action["Tags"]){{$action["Tags"]}},@endisset
                            @isset($action["Description"]) {{$action["Description"]}}@endisset
                        </li>
                        @endforeach
                        
                    </ul>
                </div>
                <div id="maneuvers" class="section">
                    <strong>Maneuvers:</strong>
                    <ul>
                        @foreach ($out['Maneuvers'] as $name=>$action)
                            @if(is_array($action))
                                <li><strong>{{$name}}</strong>
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
                <div id="responses" class="section">
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
                    <div id="rituals" class="section">
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
                <div id="perks" class="section">
                    <strong>Perks:</strong>
                    <ul>
                        @foreach($out['Perks'] as $name=>$description)
                            <li><strong>{{$name}}</strong> {{$description}}</li>
                        @endforeach
                    </ul>
                </div>

                <div id="Features" class="section">
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
                            </li>
                        @endforeach                    
                    </ul>
                </div>
                <div id="Equipment" class="section">
                <strong>Equipment:</strong>
                    <ul style="column-count: 2;">
                        @foreach($out['Equipment'] as $name=>$count)
                            <li>{{$name}} @if($count>1)x{{$count}}@endif
                        @endforeach
                    </ul>
                </div>
                <div id="Treasure" class="section">
                <strong>Treasure:</strong>
                    <ul>
                        @foreach($out['Treasure'] as $name=>$count)
                            <li>{{$name}} @if($count>1){{$count}}@endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="page">
            <h2>Compendium</h2>
            <div class="columns">
                @foreach($out["Compendium"] as $name=>$description)
                    <div class="compendium_item">
                        <h4>{{$name}}</h4>
                        <p>{{$description}}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @isset($out["Spells"])
            <div class="page">
                <h2>Spells</h2>

                <div class="columns">
                
                    @foreach($out["Spells"] as $tier => $spells)

                        @if($tier == "Cantrips")
                            <h3>Cantrips</h3>
                        @endif

                        @if($tier == "Tier 1")
                            <h3>Tier 1</h3>
                        @endif

                        @foreach($spells as $spell_name=>$spell_details)
                            <div class="spell">
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
                            </div>
                        @endforeach 
                                
                    @endforeach
                </div>
            </div>
        @endisset
    </body>
</html>