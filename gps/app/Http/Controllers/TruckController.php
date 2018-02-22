<?php

namespace App\Http\Controllers;

use App\Session;
use App\SessionPos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\APIs\Google\GoogleDirections;
use App\APIs\Google\Point;

class TruckController extends Controller
{
    /*
        Route::prefix('truck')->group(function() {
        Route::middleware('mustHaveSession')->group(function() {
            Route::post('coords', 'TruckController@sendCoords');
            Route::post('reached', 'TruckController@reachedDest');
            Route::get('destinations', 'TruckController@getDestinations');
            Route::get('inventory', 'TruckController@getInventory');
            Route::get('home', 'TruckController@viewSession');
        });
        Route::get('pickSession', 'TruckController@chooseSession')->middleware('mustHaveNoSession')->name('chooseTruck');
        Route::get('pickSession/{id}', 'TruckController@choseSession')->middleware('mustHaveNoSession')->name('chooseTruck');
    });
    */
    
    private function selectSuitableSessionsForDriver($driverId) {
        return DB::select(
            "select sessionId as id, name as vehicleName, vehicleId, count(commands.id) from commands ".
            "join sessions on sessions.id=sessionid join vehicles on vehicles.id=vehicleid ".
            "where vehicles.licence<=(select licence from drivers where id=1) ".
            "and sessions.start is null group by sessionId, name, vehicleId;"
        );
        /*return DB::select(
            "Select sessionId, name, vehicleId, COUNT(commands.ID) From commands ".
            "Join sessions On sessions.id = sessionID ".
            "Join vehicles On vehicles.id = vehicleId ".
            "Group By sessionID, name, vehicleId ".
            "Having SessionStatus(sessionId) = 0 ".
            "And CanBeCombined(?, vehicleId) = 0; ", [$driverId]
        );
        */
    }
    
    private function selectSessionClients($sessionId) {
        return DB::select(
            'select name, lat, lng from clients '.
            'join commands on clientId=clients.id where sessionId=?', [$sessionId]
        );
    }
    
    public function chooseSession(Request $req) 
    {
        // Get all available sessions
        $availableSessions = $this->selectSuitableSessionsForDriver(Auth::user()->id);
        return view('truck.pickSession', ['sessions' => $availableSessions]);
    }
    
    public function getSessionPath(Request $req)
    {
        $data = $req->validate([
            'id' => 'required|exists:sessions,id'
        ]);
        $id = $data['id'];
        return json_encode($this->selectSessionClients($id));
    }
    
    public function choseSession(Request $req, $id)
    {
        $intId = (int)$id;
        
        if (!(isset($id) && $intId > 0)) {
            return Response::json(["Invalid vehicle id : $intId"], 403);
        }
        
        // Check that the vehicle ID is valid
        $isValid = DB::select(
            "select ".
                "if((select count(v.id) from vehicles v join sessions s on s.vehicleId=v.id where s.end is null and v.id=:id)=0 ".
                "and (select count(ve.id) from vehicles ve where ve.id=:id)=1, 1, 0) as isValid;",
            ['id' => $intId]);
        
        if (!isset($isValid[0]) || $isValid[0]->isValid != 1) {
            return Response::json(["The chosen vehicle either does not exist or is currently in use. $intId" . json_encode($isValid)], 403);
        }
        
        $driverId = Auth::user()->id;
        $vehicleId = $id;
        $date = date('Y-m-d h:i:s');
        
        // Make the session object in the database
        $dat = [
            'driverId' => $driverId,
            'vehicleId' => $vehicleId,
            'start' => $date,
        ];
        $sessionId = Session::create($dat)->id;
        $req->session()->put('sessionId', $sessionId);
        
        
        // Make sure the chosen truck is available
        return redirect('truck/session');
    }
    
    public function getInventory(Request $req) 
    {
        $sessionId = $req->session()->get('sessionId');
        
        $items = DB::select(
            "select i.name, vi.amount from vehicle_items vi join vehicles v on v.id=vi.vehicleId ".
            "join sessions s on s.vehicleId=v.id join items i on i.id=vi.itemid where s.id=$sessionId;"
        );
        
        $data = [];
        foreach ($items as $i) {
            array_push($data, [$i->name, $i->amount]);
        }
        return json_encode($data);
    }
    
    public function sendCoords(Request $req) 
    {
        $data = $req->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'moment' => 'required|integer',
        ]);
        $date = date('Y-m-d h:i:s', $data['moment']);
        $sessionId = $req->session()->get('sessionId');
        $dat = [
            'sessionId' => $sessionId,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'moment' => $date,
        ];
        SessionPos::create($dat);
        
        return Response::json($dat, 201);
    }
    
    public function viewSession(Request $req) 
    {
        $sessionId = $req->session()->get('sessionId');
        
        // Get truck ID, truck info (conditioning, capacity, usedCapacity)
        $truck = DB::select("select v.* from vehicles v join sessions s on s.vehicleId=v.id where s.id=$sessionId;");
        if (count($truck) != 1) {
            return Response::json("Invalid truck data.", 500);
        }
            
        $truckId = $truck[0]->id;

        // Get current inventory
        $inventory = DB::select(
            "select i.name as name, vi.amount as amount from vehicle_items vi ".
            "join items i on i.id=vi.itemId join vehicles v on v.id=vi.vehicleId where v.id=$truckId"
        );
        
        // Get current assigned destinations (and what to drop off)
        $destinations = DB::select(
            "select cli.* from clients cli join commands c on c.clientId=cli.id join session_commands sc on sc.commandId=c.id where sc.sessionId=$sessionId;"
        );
        
        // Get current position
        $currPos = DB::select(
            "select sp1.* from session_pos sp1 where sp1.sessionId=$sessionId ".
            "and sp1.moment > DATE_SUB(NOW(), INTERVAL 1 DAY) and sp1.moment in (select max(sp2.moment) ".
            "from session_pos sp2 where sp2.sessionId=$sessionId);"
        );
        
        // Defaults to CLL (should probably move all this to config files)
        if (count($currPos) < 1) {
            $currPos = [(object)[
                'id' => -1,
                'lat' => 46.817715,
                'lng' => -71.148714,
                'moment' => date('Y-m-d h:i:s'),
            ]];
        }
        
        /*
         [46.817715,-71.148714]
         [46.820211,-71.127308]
         [46.812107,-71.137167]
         */
        
        // Map
        // Get directions
        $directions = GoogleDirections::getDirections(new Point(46.817715, -71.148714), new Point(46.820211, -71.127308), [new Point(46.812107, -71.137167)]);
        
        return view('debug', [
            'truck' => $truck, 
            'inv' => $inventory, 
            'destinations' => $destinations, 
            'currPos' => $currPos, 
            'directions' => $directions,
        ]);
    }
}
