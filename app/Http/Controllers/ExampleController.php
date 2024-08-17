<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use function PHPUnit\Framework\fileExists;

class ExampleController extends Controller
{
    public function __invoke(Request $request, $example)
    {
        if(fileExists(database_path("/sources/examples.json"))){
            $examples = json_decode(file_get_contents(database_path("/sources/examples.json")), true);
            if(isset($examples[$example])){
                return redirect()->action([CharacterSheetController::class], ["encoded"=>$examples[$example]]);
            }
        }
        return abort(404);
    }
}
