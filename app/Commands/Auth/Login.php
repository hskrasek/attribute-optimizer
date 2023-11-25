<?php

namespace App\Commands\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

use function Termwind\terminal;

#[AsCommand(name: 'auth:login')]
class Login extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'auth:login';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Authenticate with the ESI';

    /**
     * The environment variables that should be passed from host machine to the PHP server process.
     *
     * @var string[]
     */
    public static array $passthroughVariables = [
        'APP_ENV',
        'IGNITION_LOCAL_SITES_PATH',
        'LARAVEL_SAIL',
        'PATH',
        'PHP_CLI_SERVER_WORKERS',
        'PHP_IDE_CONFIG',
        'SYSTEMROOT',
        'XDEBUG_CONFIG',
        'XDEBUG_MODE',
        'XDEBUG_SESSION',
    ];

    private bool $serverRunningHasBeenDisplayed = false;

    /**
     * The list of requests being handled and their start time.
     *
     * @var array<int, Carbon>
     */
    protected array $requestsPool;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $redirectUri = 'https://login.eveonline.com/v2/oauth/authorize/';

        $params = [
            'response_type' => 'code',
            'redirect_uri' => 'http://127.0.0.1:8000',
            'client_id' => config('esi.auth.client_id'),
            'scope' => config('esi.auth.scopes'),
            'state' => $state = Str::random(),
        ];

        (new Process(['open', "$redirectUri?" . http_build_query($params)]))->start();

        $process = $this->startProcess(true);

        while ($process->isRunning()) {
            usleep(microseconds: 500 * 1000);
        }

        $exitCode = $process->getExitCode();

        return $exitCode;
    }

    /**
     * Start a new server process.
     *
     * @param bool $hasEnvironment
     * @return Process
     */
    protected function startProcess(bool $hasEnvironment): Process
    {
        $process = new Process($this->serverCommand(), base_path(), collect($_ENV)->mapWithKeys(function ($value, $key) use ($hasEnvironment) {
            if (!$hasEnvironment) {
                return [$key => $value];
            }

            return in_array($key, static::$passthroughVariables) ? [$key => $value] : [$key => false];
        })->all());

        $process->start($this->handleProcessOutput());

        return $process;
    }

    /**
     * Get the full server command.
     *
     * @return array
     */
    protected function serverCommand(): array
    {
        return [
            (new PhpExecutableFinder())->find(false),
            '-S',
            Env::get('SERVER_HOST', '127.0.0.1') . ':8000',
            __DIR__ . '/../../server.php'
        ];
    }

    /**
     * Returns a "callable" to handle the process output.
     *
     * @return callable(string, string): void
     */
    protected function handleProcessOutput(): callable
    {
        return fn($type, $buffer) => str($buffer)->explode("\n")->each(function ($line) {
            if (str($line)->contains('Development Server (http')) {
                if ($this->serverRunningHasBeenDisplayed) {
                    return;
                }

                $this->components->info("Waiting on authorization code from the browser");
                $this->comment('  <fg=yellow;options=bold>Press Ctrl+C to cancel login</>');

                $this->newLine();

                $this->serverRunningHasBeenDisplayed = true;
            } elseif (str($line)->contains(' Accepted')) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort] = [
                    $this->getDateFromLine($line),
                    false,
                ];
            } elseif (str($line)->contains([' [200]: GET '])) {
                $requestPort = $this->getRequestPortFromLine($line);

                $this->requestsPool[$requestPort][1] = trim(explode('[200]: GET', $line)[1]);
            } elseif (str($line)->contains(' Closing')) {
                $requestPort = $this->getRequestPortFromLine($line);

                if (empty($this->requestsPool[$requestPort])) {
                    return;
                }

                [$startDate, $file] = $this->requestsPool[$requestPort];

                $formattedStartedAt = $startDate->format('Y-m-d H:i:s');

                unset($this->requestsPool[$requestPort]);

                [$date, $time] = explode(' ', $formattedStartedAt);

                $this->output->write("  <fg=gray>$date</> $time");

                $runTime = $this->getDateFromLine($line)->diffInSeconds($startDate);

                if ($file) {
                    $this->output->write($file = " $file");
                }

                $dots = max(terminal()->width() - mb_strlen($formattedStartedAt) - mb_strlen($file) - mb_strlen($runTime) - 9, 0);

                $this->output->write(' ' . str_repeat('<fg=gray>.</>', $dots));
                $this->output->writeln(" <fg=gray>~ {$runTime}s</>");
            } elseif (str($line)->contains(['Closed without sending a request'])) {
                // ...
            } elseif (!empty($line)) {
                $position = strpos($line, '] ');

                if ($position !== false) {
                    $line = substr($line, $position + 1);
                }

                $this->components->warn($line);
            }
        });
    }

    /**
     * Get the date from the given PHP server output.
     *
     * @param string $line
     * @return Carbon
     */
    protected function getDateFromLine(string $line): Carbon
    {
        $regex = env('PHP_CLI_SERVER_WORKERS', 1) > 1
            ? '/^\[\d+]\s\[([a-zA-Z0-9: ]+)\]/'
            : '/^\[([^\]]+)\]/';

        $line = str_replace('  ', ' ', $line);

        preg_match($regex, $line, $matches);

        return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
    }

    /**
     * Get the request port from the given PHP server output.
     *
     * @param string $line
     * @return int
     */
    protected function getRequestPortFromLine(string $line): int
    {
        preg_match('/:(\d+)\s(?:(?:\w+$)|(?:\[.*))/', $line, $matches);

        return (int) $matches[1];
    }
}
