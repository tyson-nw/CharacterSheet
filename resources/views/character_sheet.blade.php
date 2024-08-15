<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>

    </head>



    <body>

        @php
            dump($out);
        @endphp
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

            <div id="actions">
                <strong>Actions:</strong>
                <ul>
                    @foreach ($out['Actions'] as $action)
                    <li>
                        <strong>{{$action['Title']}}</strong> 
                        
                        @isset($action["Vs"])
                        +{{$action['Bonus']}} vs. {{$action["Vs"]}}
                        @endisset
                        @isset($action["Damage"])
                            ({{$action["Damage"]}}@isset($action["Additional Damage"])+{{implode("+", $action["Additional Damage"])}}@endisset+{{$action['Dmg Bonus']}} Damage)
                        @endisset 
                        @isset($action["Tags"]){{$action["Tags"]}}@endisset
                        @isset($action["Description"])- {{$action["Description"]}}@endisset
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
                                @isset($action["Tags"]){{$action["Tags"]}}@endisset
                                @isset($action["Description"])- {{$action["Description"]}}@endisset
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
                                @isset($action["Tags"]){{$action["Tags"]}}@endisset
                                @isset($action["Description"])- {{$action["Description"]}}@endisset
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
                                    @isset($action["Tags"]){{$action["Tags"]}}@endisset
                                    @isset($action["Description"])- {{$action["Description"]}}@endisset
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
                    @foreach($out['Features'] as $name=>$description)
                        <li><strong>{{$name}}</strong> {{$description}}</li>
                    @endforeach
                </ul>
        </div>
        <div class="page">
            <h2>Compendium</h2>
            @foreach($out["Compendium"] as $name=>$description)
                <h3>{{$name}}</h3>
                <p>{{$description}}</p>
            @endforeach
        </div>
        @isset ($out["Spells"])
        <div class="page">
            <h2>Spells</h2>
        </div>
        @endisset



    </body>
</html>