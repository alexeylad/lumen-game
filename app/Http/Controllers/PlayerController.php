<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function action(int $playerId, Request $request): JsonResponse
    {
        $this->validate($request, [
            'player_lvl' => 'required|integer',
            'monsters' => 'required|array',
            'server_time' => 'required|date',
        ]);

        $player = new Player($playerId, $request->player_lvl, $request->server_time);

        $hid = $player->attack($request->monsters);

        return response()->json([
            'player' => $player,
            'hid_monsters' => $hid,
        ], JsonResponse::HTTP_OK);
    }
}
