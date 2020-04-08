<?php

namespace App\Models;

use Carbon\Carbon;

class Player extends BaseModel
{
    public const PLAYERS_JSON = '/app/players.json';

    /**
     * Skill 'monster hidden'.
     */
    public const SKILL_HIDE_MIN = 0;
    public const SKILL_HIDE_MAX = 1500;

    public const SKILL_HIDE_RANGES = [
        [
            'min' => 0,
            'max' => 24,
            'hide_lvl' => 0,
        ],
        [
            'min' => 25,
            'max' => 49,
            'hide_lvl' => -4,
        ],
        [
            'min' => 50,
            'max' => 74,
            'hide_lvl' => -3,
        ],
        [
            'min' => 75,
            'max' => 99,
            'hide_lvl' => -2,
        ],
        [
            'min' => 100,
            'max' => 100,
            'hide_lvl' => null,
        ],
    ];

    /**
     * Increase 'hide monsters' skills after attack.
     */
    public const SKILL_HIDE_INCREASE_LVL_LARGER = 2;
    public const SKILL_HIDE_INCREASE_SHIFT_VALUE = 3;

    /**
     * Decrease 'hide monsters' skills per hour.
     */
    public const SKILL_HIDE_DECREASE_PER_HOUR = 60;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $lvl;

    /**
     * @var int
     */
    public $skillHide;

    public function __construct(int $id, int $playerLvl, string $serverTime)
    {
        $arr = self::index();

        $this->id = $id;
        $this->lvl = $playerLvl;

        $data = array_filter($arr['players'], function ($players) use ($id) {
            return $players['id'] === $id;
        });

        array_map(function ($data) {
            $this->setSkillHide($data['skill_hide']);
        }, $data);

        $this->decreaseSkillHide($serverTime);
    }

    public function setSkillHide(int $value)
    {
        if ($value > self::SKILL_HIDE_MAX) {
            $this->skillHide = self::SKILL_HIDE_MAX;
        } elseif ($value < self::SKILL_HIDE_MIN) {
            $this->skillHide = self::SKILL_HIDE_MIN;
        }

        $this->skillHide = $value;
    }

    public static function index(): array
    {
        $path = self::getStoragePath();

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Get 'hide monsters' skill influence.
     */
    public function getSkillHideInfluence(): ?int
    {
        $skillInfluence = $this->skillHide * 100 / self::SKILL_HIDE_MAX; // Convert to percentage

        foreach (self::SKILL_HIDE_RANGES as $range) {
            if ($skillInfluence >= $range['min'] && $skillInfluence <= $range['max']) {
                return $range['hide_lvl'];
            }
        }

        return null;
    }

    public function attack(array $monsters): array
    {
        $hideInfluence = $this->getSkillHideInfluence();

        if ($hideInfluence === null) {
            return $monsters;
        }

        $hideLvl = $this->lvl + $hideInfluence; // Get monsters lvls who will be hidden

        foreach ($monsters as $key => $monster) {
            if ($monster['lvl'] > $hideLvl || $hideInfluence === 0) {
                $this->increaseSkillHide($monster['lvl']);
                unset($monsters[$key]);
            }
        }

        return $monsters;
    }

    /**
     * Increase skill after the win.
     */
    public function increaseSkillHide(int $monsterLvl): void
    {
        $diff = $this->lvl - $monsterLvl;

        if ($diff < 0) {
            $this->setSkillHide($this->skillHide + self::SKILL_HIDE_INCREASE_LVL_LARGER);
        } else {
            $this->setSkillHide($this->skillHide + self::SKILL_HIDE_INCREASE_SHIFT_VALUE);
        }
    }

    /**
     * Decrease skill every hour.
     */
    public function decreaseSkillHide(string $serverTime): void
    {
        $diff = Carbon::parse($serverTime)->diffInHours(Carbon::now());

        $refreshedValue = $this->skillHide - self::SKILL_HIDE_DECREASE_PER_HOUR * $diff;

        $this->setSkillHide($refreshedValue);
    }

    public static function getStoragePath(): string
    {
        return storage_path() . self::PLAYERS_JSON;
    }
}
