<?php

namespace App\Commands\Skills;

use App\ESI\Auth\Token;
use App\ESI\Http\Middleware;
use App\Type;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'skills:queue')]
class Queue extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'skills:queue';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'View your current Eve Online skill queue';

    public function handle(): int
    {
        if (Storage::has('token.json')) {
            /** @var Token $token */
            $token = Token::make(Storage::get('token.json'));
        } else {
            $this->error('No token found. Please run auth:login first.');

            return Command::FAILURE;
        }

        $skillQueue = Http::withHeaders([
            //TODO: Better
            'User-Agent' => 'Eve-Online-CLI-App',
            'Authorization' => 'Bearer ' . $token->accessToken,
        ])->withMiddleware(Middleware::refreshToken($token))->get(
            'https://esi.evetech.net/latest/characters/' . $token->characterId() . '/skillqueue'
        )
            ->throw()
            ->json();

        /** @var Collection<string, Type> $skills */
        $skills = Type::with('group')
            ->whereIn('typeID', Arr::pluck($skillQueue, 'skill_id'))
            ->get()
            ->keyBy('typeID');

        $this->table(
            ['Group', 'Skill', 'Level', 'Finishes In',],
            array_map(fn(array $skill) => [
                $skills->get($skill['skill_id'])->group->groupName,
                $skills->get($skill['skill_id'])->typeName,
                $skill['finished_level'],
                Carbon::parse($skill['finish_date'])->diffForHumans(),
            ], $skillQueue)
        );

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
