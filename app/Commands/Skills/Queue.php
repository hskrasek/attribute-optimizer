<?php

namespace App\Commands\Skills;

use App\ESI\Auth\Token;
use App\ESI\Http\Middleware;
use App\ESI\SDE;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Termwind\render;

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
        ])->withMiddleware(Middleware::refreshToken($token))->get('https://esi.evetech.net/latest/characters/' . $token->characterId() . '/skillqueue')
            ->throw()
            ->json();

        //TODO: Look into use Termwind for this
        $this->table(
            ['Skill', 'Level', 'Start Time', 'Finish Time', 'Training Start SP', 'Training Destination SP',],
            array_map(fn (array $skill) => [
                SDE::types($skill['skill_id'])['name']['en'],
                $skill['finished_level'],
                $skill['start_date'],
                $skill['finish_date'],
                $skill['training_start_sp'],
                $skill['level_end_sp'],
            ], $skillQueue)
        );

        return Command::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
