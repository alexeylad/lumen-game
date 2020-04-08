<?php

use Carbon\Carbon;

class ActionTest extends TestCase
{
    const MONSTERS_PAYLOAD = [
        'monsters' => [
            ['id' => 1, 'lvl' => 1],
            ['id' => 2, 'lvl' => 1],
            ['id' => 3, 'lvl' => 1],
            ['id' => 4, 'lvl' => 2],
            ['id' => 5, 'lvl' => 2],
            ['id' => 6, 'lvl' => 2],
            ['id' => 7, 'lvl' => 3],
            ['id' => 8, 'lvl' => 3],
            ['id' => 9, 'lvl' => 3],
            ['id' => 10, 'lvl' => 4],
            ['id' => 11, 'lvl' => 4],
            ['id' => 12, 'lvl' => 4],
            ['id' => 13, 'lvl' => 5],
            ['id' => 14, 'lvl' => 5],
            ['id' => 15, 'lvl' => 5],
            ['id' => 16, 'lvl' => 6],
            ['id' => 17, 'lvl' => 6],
            ['id' => 18, 'lvl' => 6],
            ['id' => 19, 'lvl' => 7],
            ['id' => 20, 'lvl' => 8],
        ],
    ];

    public function testAction(): void
    {
        $response = $this->post('/', [
            'player_lvl' => 5,
            self::MONSTERS_PAYLOAD,
            'server_time' => Carbon::now()->toString(),
        ]);

        $this->assertEquals(
            // TODO
        );
    }
}
